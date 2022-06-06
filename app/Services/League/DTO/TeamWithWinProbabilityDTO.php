<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class TeamWithWinProbabilityDTO extends DataTransferObject
{
    public int $id;
    public string $name;
    public int $winProbability;
}
