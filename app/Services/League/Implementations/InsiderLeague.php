<?php

namespace App\Services\League\Implementations;

use App\Models\LeagueMatch;
use Illuminate\Database\Eloquent\Collection;

class InsiderLeague implements LeagueImplementation
{
    public function getTeamsQuantity(): int
    {
        return 4;
    }

    public function getWeeksQuantity(): int
    {
        return $this->getTeamsQuantity() * 2 - 2;
    }

    public function getWinPoints(): int
    {
        return 3;
    }

    public function getDrawPoints(): int
    {
        return 1;
    }

    public function getLossPoints(): int
    {
        return 0;
    }

    public function getFixtures(Collection $teams): Collection
    {
        $teamsQuantity = $this->getTeamsQuantity();
        $weeksQuantity = $this->getWeeksQuantity();
        $halfOfTeamsCount = $teamsQuantity / 2;
        $halfOfWeeksCount = $weeksQuantity / 2;
        $weekMatches = Collection::make();
        $teamIds = $teams->pluck('id')->all();
        $currentWeek = 1;

        while ($currentWeek <= $weeksQuantity) {
            $firstTeamIsHouse = $currentWeek <= $halfOfWeeksCount;
            $index = 0;

            while ($index < $halfOfTeamsCount) {
                $team1 = $teamIds[$index];
                $team2 = $teamIds[$index + $halfOfTeamsCount];

                $weekMatch = new LeagueMatch(['week' => $currentWeek]);
                $weekMatch->houseTeam()->associate($firstTeamIsHouse ? $team1 : $team2);
                $weekMatch->awayTeam()->associate($firstTeamIsHouse ? $team2 : $team1);

                $weekMatches->push($weekMatch);
                $index++;
            }

            $teamIds = $this->roundRobinRotation($teamIds);
            $currentWeek++;
        }

        return $weekMatches;
    }

    protected function roundRobinRotation(array $teamIds): array
    {
        $lastIndex = count($teamIds) - 1;
        $newTeams = [$teamIds[0]];

        for($i = 1; $i <= $lastIndex; $i++) {
            $newIndex = $i + 1;

            if ($newIndex > $lastIndex) {
                $newIndex -= $lastIndex;
            }

            $newTeams[$newIndex] = $teamIds[$i];
        }

        return $newTeams;
    }

    public function getValidationRules(): array
    {
        return [
            'teams' => [
                'sometimes',
                'array',
                'min:4',
                function ($attribute, $value, $fail) {
                    if (!is_array($value) || (count($value) % 2 !== 0)) {
                        $fail('There should be even number of teams.');
                    }
                },
            ]
        ];
    }
}
