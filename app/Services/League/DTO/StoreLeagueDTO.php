<?php

namespace App\Services\League\DTO;

use App\Services\League\DTO\Casters\LeagueTypeEnumCaster;
use App\Services\League\Enums\LeagueTypeEnum;
use Illuminate\Http\Request;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

#[Strict]
class StoreLeagueDTO extends DataTransferObject
{
    public string $name;

    #[CastWith(LeagueTypeEnumCaster::class)]
    public LeagueTypeEnum $type;

    /** @var TeamDTO[] */
    #[CastWith(ArrayCaster::class, TeamDTO::class)]
    public array $teams;

    /**
     * @throws UnknownProperties
     */
    public static function fromRequest(Request $request): StoreLeagueDTO
    {
        return new self(
            name: $request->input('name'),
            type: $request->input('type'),
            teams: $request->input('teams', [])
        );
    }
}
