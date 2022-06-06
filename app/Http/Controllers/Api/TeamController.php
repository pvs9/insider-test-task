<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\JsonResponse;

class TeamController extends ApiController
{
    public function all(): JsonResponse
    {
        return $this->okResponse(
            TeamResource::collection(
                Team::query()->get()
            )
        );
    }
}
