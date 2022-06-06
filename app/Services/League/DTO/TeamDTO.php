<?php

namespace App\Services\League\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class TeamDTO extends DataTransferObject
{
    public int $id;
    public string $name;
}
