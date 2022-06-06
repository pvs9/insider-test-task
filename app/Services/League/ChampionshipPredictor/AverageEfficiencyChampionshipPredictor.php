<?php

namespace App\Services\League\ChampionshipPredictor;

use App\Models\LeagueMatch;
use App\Models\Team;
use App\Services\League\DTO\LeaguePredictionResultsDTO;
use App\Services\League\DTO\LeagueSettingsDTO;
use App\Services\League\DTO\TeamWithWinProbabilityDTO;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AverageEfficiencyChampionshipPredictor implements ChampionshipPredictor
{
    /**
     * @throws UnknownProperties
     */
    public function predictChampionship(
        Collection $teams,
        Collection $matches,
        LeagueSettingsDTO $settings,
        int $weekNumber
    ): LeaguePredictionResultsDTO {
        $coefficients = [];
        $totalCoefficient = 0.0;
        $totalGoalDifference = $teams->reduce(function ($carry, $team) {
            return $carry + abs($team->progress->goal_difference);
        }, 0);

        /** @var Team $team */
        foreach ($teams as $team) {
            /** @var LeagueMatch $nextWeekMatch */
            $nextWeekMatch = $matches->filter(function (LeagueMatch $match, $key) use ($team, $weekNumber) {
                return $match->week === $weekNumber + 1 &&
                    ($match->houseTeam->id === $team->id || $match->awayTeam->id === $team->id);
            })
            ->first();
            $isPlayingHouse = $nextWeekMatch->houseTeam->id === $team->id;
            /** @var LeagueMatch $previousMatch */
            $previousMatch = $matches->filter(
                function (LeagueMatch $match, $key) use ($isPlayingHouse, $team, $weekNumber) {
                    return $match->week <= $weekNumber && ($isPlayingHouse ?
                        $match->awayTeam->id === $team->id :
                        $match->houseTeam->id === $team->id);
                }
            )
                ->first();
            $goalDifference = $isPlayingHouse ?
                $previousMatch->away_points - $previousMatch->house_points :
                $previousMatch->house_points - $previousMatch->away_points;

            $averageWinProbability = $team->progress->wins / $team->progress->played;
            $averageDrawProbability = $team->progress->draws / $team->progress->played;
            $averageLossProbability = $team->progress->losses / $team->progress->played;

            if ($goalDifference > 0) {
                ++$averageWinProbability;
            } elseif ($goalDifference === 0) {
                ++$averageDrawProbability;
            } else {
                ++$averageLossProbability;
            }

            $coefficient = (($averageWinProbability * $settings->winPoints +
                $averageDrawProbability * $settings->drawPoints +
                $averageLossProbability * $settings->lossPoints) / 2);

            if ($totalGoalDifference > 0) {
                $coefficient += $team->progress->goal_difference / $totalGoalDifference;
            }

            $coefficients[$team->id] = $coefficient;

            if ($coefficient > 0) {
                $totalCoefficient += $coefficient;
            }
        }

        $dtos = [];

        foreach ($teams as $team) {
            $probability = (int) round($coefficients[$team->id] / $totalCoefficient * 100);

            $dtos[] = new TeamWithWinProbabilityDTO(
                id: $team->id,
                name: $team->name,
                winProbability: max($probability, 0)
            );
        }

        return new LeaguePredictionResultsDTO(
            teams: $dtos
        );
    }
}
