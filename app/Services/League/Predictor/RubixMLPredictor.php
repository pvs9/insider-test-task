<?php

namespace App\Services\League\Predictor;

use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\DTO\LeagueMatchPredictionResultsDTO;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class RubixMLPredictor implements Predictor
{
    /**
     * @throws UnknownProperties
     */
    public function predictMatch(LeagueMatchPredictionDTO $dto): LeagueMatchPredictionResultsDTO
    {
        if (
            !Storage::exists('datasets/prepared/house_model.rbx') ||
            !Storage::exists('datasets/prepared/away_model.rbx')
        ) {
            throw new PreconditionFailedHttpException('No trained Rubix ML models found in the storage');
        }

        $houseDataset = [[
            'HouseTeam' => $dto->houseTeam->name,
            'AwayTeam' => $dto->awayTeam->name,
            'HouseTeamRating' => $dto->houseTeam->rating,
            'AwayTeamRating' => $dto->awayTeam->rating,
            'HouseTeamScore' => 0,
        ]];
        $houseDataset = Labeled::fromIterator($houseDataset);
        $houseEstimator = PersistentModel::load(new Filesystem(storage_path('app/datasets/prepared/house_model.rbx')));

        $awayDataset = [[
            'HouseTeam' => $dto->houseTeam->name,
            'AwayTeam' => $dto->awayTeam->name,
            'HouseTeamRating' => $dto->houseTeam->rating,
            'AwayTeamRating' => $dto->awayTeam->rating,
            'AwayTeamScore' => 0,
        ]];
        $awayDataset = Labeled::fromIterator($awayDataset);
        $awayEstimator = PersistentModel::load(new Filesystem(storage_path('app/datasets/prepared/away_model.rbx')));

        return new LeagueMatchPredictionResultsDTO(
            housePoints: $houseEstimator->predict($houseDataset)[0],
            awayPoints: $awayEstimator->predict($awayDataset)[0]
        );
    }
}
