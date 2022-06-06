<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class LeagueSettingsDTO extends DataTransferObject
{
    public int $winPoints;
    public int $drawPoints;
    public int $lossPoints;
}
