<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $week
 * @property int|null $away_points
 * @property int|null $house_points
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Team $awayTeam
 * @property-read League $league
 * @property-read Team $houseTeam
 */
class LeagueMatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'week', 'away_points', 'house_points',
    ];

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function houseTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
