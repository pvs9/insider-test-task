<?php

namespace App\Services\League\Predictor;

use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\DTO\LeagueMatchPredictionResultsDTO;

interface Predictor
{
    public function predictMatch(LeagueMatchPredictionDTO $dto): LeagueMatchPredictionResultsDTO;
}
