<?php

namespace App\Services\League\TeamsDrafter;

use Illuminate\Database\Eloquent\Collection;

interface TeamsDrafter
{
    public function draft(int $quantity): Collection;
}
