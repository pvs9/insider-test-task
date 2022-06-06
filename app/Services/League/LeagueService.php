<?php

namespace App\Services\League;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Services\League\ChampionshipPredictor\ChampionshipPredictor;
use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\DTO\LeagueSettingsDTO;
use App\Services\League\DTO\StoreLeagueDTO;
use App\Services\League\Enums\LeagueTypeEnum;
use App\Services\League\Exceptions\LeagueNotSetException;
use App\Services\League\Implementations\InsiderLeague;
use App\Services\League\Implementations\LeagueImplementation;
use App\Services\League\Predictor\Predictor;
use App\Services\League\TeamsDrafter\TeamsDrafter;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

final class LeagueService
{
    protected LeagueImplementation $leagueImplementation;

    protected League $league;

    public function __construct(
        protected ChampionshipPredictor $championshipPredictor,
        protected Predictor $predictor,
        protected TeamsDrafter $drafter
    ) {
    }

    protected function checkLeague(): void
    {
        if (is_null($this->getLeague())) {
            throw new LeagueNotSetException();
        }
    }

    public function getLeague(): ?League
    {
        return $this->league ?? null;
    }

    public function setLeague(League $league): LeagueService
    {
        $this->league = $league;
        $this->setLeagueImplementation(
            $this->getLeagueImplementationFromType($league->type)
        );

        return $this;
    }

    public function getLeagueImplementation(): ?LeagueImplementation
    {
        return $this->leagueImplementation ?? null;
    }

    public function setLeagueImplementation(LeagueImplementation $leagueImplementation): LeagueService
    {
        $this->leagueImplementation = $leagueImplementation;

        return $this;
    }

    public function getLeagueImplementationFromType(LeagueTypeEnum $type): LeagueImplementation
    {
        $implementationClass = match ($type) {
            LeagueTypeEnum::INSIDER_LEAGUE => InsiderLeague::class,
        };

        return resolve($implementationClass);
    }

    public function getStoreValidationRules(StoreLeagueDTO $dto): array
    {
        return $this->getLeagueImplementationFromType($dto->type)
            ->getValidationRules();
    }

    public function storeLeague(StoreLeagueDTO $dto): ?League
    {
        $league = new League;
        $league->name = $dto->name;
        $league->type = $dto->type;

        DB::beginTransaction();

        try {
            $league->save();

            $this->setLeague($league);
            $teams = Arr::pluck($dto->teams, 'id');

            if (empty($teams)) {
                $teams = $this->drafter->draft(
                    $this->getLeagueImplementation()?->getTeamsQuantity()
                );
            }

            $league->teams()->sync($teams);

            $this->storeFixtures();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error(
                '[LEAGUE] Error while storing the league',
                [
                    'dto' => $dto,
                    'exception' => $e,
                ]
            );

            return null;
        }

        DB::commit();

        return $league;
    }

    protected function storeFixtures(): void
    {
        $this->checkLeague();

        /** @var League $league */
        $league = $this->getLeague();
        /** @var LeagueImplementation $leagueImplementation */
        $leagueImplementation = $this->getLeagueImplementation();

        $fixtures = $leagueImplementation->getFixtures($league->teams);

        $league->matches()
            ->saveMany($fixtures);
    }

    public function progressLeague(): bool
    {
        $this->checkLeague();

        /** @var League $league */
        $league = $this->getLeague();
        $league->loadMissing('teams');
        $weekToProgress = $league->progress_week + 1;
        $matchesToPredict = $league
            ->matches
            ->where('week', $weekToProgress)
            ->loadMissing('awayTeam', 'houseTeam');


        DB::beginTransaction();

        try {
            foreach ($matchesToPredict as $matchToPredict) {
                $this->progressMatch($matchToPredict);
            }

            if ($weekToProgress >= $this->getLeagueImplementation()?->getWeeksQuantity() / 2) {
                $league->refresh();

                $this->predictChampionship($weekToProgress);
            }
        } catch (Exception $e) {
            Log::error(
                '[LEAGUE] Error while progressing league',
                [
                    'league' => $league,
                    'exception' => $e,
                ]
            );

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }

    public function progressWholeLeague(): bool
    {
        $this->checkLeague();
        /** @var League $league */
        $league = $this->getLeague();
        /** @var LeagueImplementation $leagueImplementation */
        $leagueImplementation = $this->getLeagueImplementation();

        DB::beginTransaction();

        for ($i = $league->progress_week; $i <= $leagueImplementation->getWeeksQuantity(); $i++) {
            $success = $this->progressLeague();

            if (!$success) {
                DB::rollBack();

                return false;
            }

            $league->refresh();
        }

        DB::commit();

        return true;
    }

    /**
     * @throws UnknownProperties
     */
    protected function progressMatch(LeagueMatch $match): void
    {
        $dto = new LeagueMatchPredictionDTO(
            houseTeam: $match->houseTeam->toArray(),
            awayTeam: $match->awayTeam->toArray()
        );

        $predictedResults = $this->predictor->predictMatch($dto);
        /** @var League $league */
        $league = $this->getLeague();
        /** @var LeagueImplementation $leagueImplementation */
        $leagueImplementation = $this->getLeagueImplementation();

        $match->update(
            [
                'house_points' => $predictedResults->housePoints,
                'away_points' => $predictedResults->awayPoints
             ]
        );

        $teamsWithPivots = $league->teams
            ->whereIn('id', [$match->houseTeam->id, $match->awayTeam->id]);
        $currentHousePivot = $teamsWithPivots
            ->find($match->houseTeam->id)
            ->progress;
        $currentAwayPivot = $teamsWithPivots
            ->find($match->awayTeam->id)
            ->progress;

        if ($predictedResults->awayPoints === $predictedResults->housePoints) {
            $drawPoints = $leagueImplementation->getDrawPoints();

            $league->teams()->updateExistingPivot(
                $match->houseTeam->id,
                [
                    'played' => $currentHousePivot->played + 1,
                    'draws' => $currentHousePivot->draws + 1,
                    'points' => $currentHousePivot->points + $drawPoints,
                ]
            );

            $league->teams()->updateExistingPivot(
                $match->awayTeam->id,
                [
                    'played' => $currentAwayPivot->played + 1,
                    'draws' => $currentAwayPivot->draws + 1,
                    'points' => $currentAwayPivot->points + $drawPoints,
                ]
            );
        } else {
            $houseTeamIsWinner = $predictedResults->housePoints > $predictedResults->awayPoints;

            $houseTeamPivotResults = [
                'played' => $currentHousePivot->played + 1,
                'goal_difference' => $currentHousePivot->goal_difference +
                    $predictedResults->housePoints -
                    $predictedResults->awayPoints,
            ];

            $awayTeamPivotResults = [
                'played' => $currentAwayPivot->played + 1,
                'goal_difference' => $currentAwayPivot->goal_difference +
                    $predictedResults->awayPoints -
                    $predictedResults->housePoints,
            ];

            $winPoints = $leagueImplementation->getWinPoints();
            $lossPoints = $leagueImplementation->getLossPoints();

            if ($houseTeamIsWinner) {
                $houseTeamPivotResults['wins'] = $currentHousePivot->wins + 1;
                $houseTeamPivotResults['points'] = $currentHousePivot->points +
                    $winPoints;

                $awayTeamPivotResults['losses'] = $currentAwayPivot->losses + 1;
                $awayTeamPivotResults['points'] = $currentAwayPivot->points +
                    $lossPoints;
            } else {
                $awayTeamPivotResults['wins'] = $currentAwayPivot->wins + 1;
                $awayTeamPivotResults['points'] = $currentAwayPivot->points +
                    $winPoints;

                $houseTeamPivotResults['losses'] = $currentHousePivot->losses + 1;
                $houseTeamPivotResults['points'] = $currentHousePivot->points +
                    $lossPoints;
            }

            $league->teams()->updateExistingPivot(
                $match->houseTeam->id,
                $houseTeamPivotResults
            );

            $league->teams()->updateExistingPivot(
                $match->awayTeam->id,
                $awayTeamPivotResults
            );
        }
    }

    /**
     * @throws UnknownProperties
     */
    protected function predictChampionship(int $weekNumber): void
    {
        $this->checkLeague();

        /** @var League $league */
        $league = $this->getLeague();
        /** @var LeagueImplementation $leagueImplementation */
        $leagueImplementation = $this->getLeagueImplementation();

        $orderedTeams = $league->teams->sortBy([
            ['progress.points', 'desc'],
            ['progress.goal_difference', 'desc'],
            ['name', 'asc'],
        ]);

        if (
            $weekNumber === $leagueImplementation->getWeeksQuantity() ||
            $orderedTeams->get(0)->progress->points -
            $orderedTeams->get(1)->progress->points >
            ($leagueImplementation->getWeeksQuantity() - $weekNumber) * $leagueImplementation->getWinPoints()
        ) {
            $winner = $orderedTeams->first();

            foreach ($league->teams as $team) {
                $league->teams()->updateExistingPivot(
                    $team->id,
                    [
                        'win_probability' => $winner->id === $team->id ? 100 : 0,
                    ]
                );
            }

            return;
        }

        $prediction = $this->championshipPredictor->predictChampionship(
            $league->teams,
            $league->matches,
            new LeagueSettingsDTO(
                winPoints: $leagueImplementation->getWinPoints(),
                drawPoints: $leagueImplementation->getDrawPoints(),
                lossPoints: $leagueImplementation->getLossPoints(),
            ),
            $weekNumber
        );

        foreach ($prediction->teams as $teamPrediction) {
            $league->teams()->updateExistingPivot(
                $teamPrediction->id,
                [
                    'win_probability' => $teamPrediction->winProbability,
                ]
            );
        }
    }

    public function resetLeague(): bool
    {
        $this->checkLeague();

        /** @var League $league */
        $league = $this->getLeague();

        DB::beginTransaction();

        try {
            $league->matches()->delete();
            $league->teams()->detach();

            $teams = $this->drafter->draft(
                $this->getLeagueImplementation()?->getTeamsQuantity()
            );
            $league->teams()->sync($teams);

            $this->storeFixtures();
        } catch (Exception $e) {
            Log::error(
                '[LEAGUE] Error while resetting league',
                [
                    'league' => $league,
                    'exception' => $e,
                ]
            );

            DB::rollBack();

            return false;
        }

        DB::commit();

        return true;
    }
}
