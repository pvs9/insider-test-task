<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;

class LeaguePredictionResultsDTO extends DataTransferObject
{
    /** @var TeamWithWinProbabilityDTO[] */
    #[CastWith(ArrayCaster::class, TeamWithWinProbabilityDTO::class)]
    public array $teams;
}
