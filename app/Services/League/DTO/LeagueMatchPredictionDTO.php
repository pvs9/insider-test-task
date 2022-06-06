<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class LeagueMatchPredictionDTO extends DataTransferObject
{
    public TeamWithRatingDTO $houseTeam;
    public TeamWithRatingDTO $awayTeam;
}
