<?php

namespace App\Services\League\Implementations;

use Illuminate\Database\Eloquent\Collection;

interface LeagueImplementation
{
    public function getTeamsQuantity(): int;

    public function getWeeksQuantity(): int;

    public function getWinPoints(): int;

    public function getDrawPoints(): int;

    public function getLossPoints(): int;

    public function getFixtures(Collection $teams): Collection;

    public function getValidationRules(): array;
}
