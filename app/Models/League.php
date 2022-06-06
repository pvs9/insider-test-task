<?php

namespace App\Models;

use App\Services\League\Enums\LeagueTypeEnum;
use App\Services\League\LeagueService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property LeagueTypeEnum $type
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Collection|LeagueMatch[] $matches
 * @property-read Collection|Team[] $teams
 *
 * @property-read int|null $progress_week
 * @property-read int|null $total_weeks
 */
class League extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type', 'name',
    ];

    protected $casts = [
        'type' => LeagueTypeEnum::class,
    ];

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->inRandomOrder()
            ->as('progress')
            ->withPivot(
                'points',
                'played',
                'wins',
                'draws',
                'losses',
                'goal_difference',
                'win_probability',
            );
    }

    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class)
            ->orderBy('week');
    }

    public function getProgressWeekAttribute(): ?int
    {
        return $this->matches
            ->whereNotNull('house_points')
            ->max('week');
    }

    public function getTotalWeeksAttribute(): ?int
    {
        return app(LeagueService::class)
            ->getLeagueImplementationFromType($this->type)
            ->getWeeksQuantity();
    }
}
