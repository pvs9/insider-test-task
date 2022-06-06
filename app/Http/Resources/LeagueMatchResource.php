<?php

namespace App\Http\Resources;

use App\Models\LeagueMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LeagueMatch
 */
class LeagueMatchResource extends JsonResource
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
            'week' => $this->week,
            'away_team' => TeamResource::make($this->whenLoaded('awayTeam')),
            'house_team' => TeamResource::make($this->whenLoaded('houseTeam')),
            'away_points' => $this->away_points,
            'house_points' => $this->house_points,
        ];
    }
}
