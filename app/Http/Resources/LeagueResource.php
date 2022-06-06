<?php

namespace App\Http\Resources;

use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin League
 */
class LeagueResource extends JsonResource
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
            'matches' => LeagueMatchResource::collection($this->whenLoaded('matches')),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
        ];
    }
}
