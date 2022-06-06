<?php

namespace App\Services\League\TeamsDrafter;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class RandomTeamsDrafter implements TeamsDrafter
{
    public function draft(int $quantity): Collection
    {
        return Team::query()
            ->inRandomOrder()
            ->take($quantity)
            ->get();
    }
}
