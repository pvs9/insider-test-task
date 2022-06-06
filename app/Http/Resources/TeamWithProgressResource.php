<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class TeamWithProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            $this->mergeWhen(
                $this->resource->progress && $this->resource->progress->getTable() === 'league_team',
                [
                    'points' => $this->progress->points,
                    'played' => $this->progress->played,
                    'wins' => $this->progress->wins,
                    'draws' => $this->progress->draws,
                    'losses' => $this->progress->losses,
                    'goal_difference' => $this->progress->goal_difference,
                    'win_probability' => $this->progress->win_probability,
                ]
            ),
        ];
    }
}
