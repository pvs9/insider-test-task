<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class LeagueMatchPredictionResultsDTO extends DataTransferObject
{
    public int $housePoints;
    public int $awayPoints;
}
