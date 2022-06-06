<?php

namespace App\Services\League\Predictor;

use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\DTO\LeagueMatchPredictionResultsDTO;
use Exception;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class RandomPredictor implements Predictor
{

    /**
     * @throws UnknownProperties
     * @throws Exception
     */
    public function predictMatch(LeagueMatchPredictionDTO $dto): LeagueMatchPredictionResultsDTO
    {
        return new LeagueMatchPredictionResultsDTO(
            housePoints: random_int(0, 3),
            awayPoints: random_int(0, 3)
        );
    }
}
