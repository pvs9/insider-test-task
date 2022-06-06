<?php

namespace App\Services\League\ChampionshipPredictor;

use App\Services\League\DTO\LeaguePredictionResultsDTO;
use App\Services\League\DTO\LeagueSettingsDTO;
use Illuminate\Database\Eloquent\Collection;

interface ChampionshipPredictor
{
    public function predictChampionship(
        Collection $teams,
        Collection $matches,
        LeagueSettingsDTO $settings,
        int $weekNumber
    ): LeaguePredictionResultsDTO;
}
