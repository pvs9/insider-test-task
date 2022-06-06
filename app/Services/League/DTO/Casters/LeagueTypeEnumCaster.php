<?php

namespace App\Services\League\DTO\Casters;

use App\Services\League\Enums\LeagueTypeEnum;
use Spatie\DataTransferObject\Caster;

class LeagueTypeEnumCaster implements Caster
{
    public function cast(mixed $value): mixed
    {
        return LeagueTypeEnum::tryFrom($value);
    }
}
