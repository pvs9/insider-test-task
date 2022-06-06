<?php

namespace App\Http\Resources;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin League
 */
class LeagueWithProgressResource extends JsonResource
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
            'type' => $this->type,
            'name' => $this->name,
            'progress_week' => $this->progress_week,
            'total_weeks' => $this->total_weeks,
            'matches' => LeagueMatchResource::collection($this->whenLoaded('matches')),
            'teams' => TeamWithProgressResource::collection(
                $this->whenLoaded(
                    'teams',
                    function () {
                        return $this->teams->sortByDesc('progress.points');
                    }
                )
            ),
        ];
    }
}
