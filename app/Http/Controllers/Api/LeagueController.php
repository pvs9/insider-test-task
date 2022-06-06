<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\League\StoreLeagueRequest;
use App\Http\Resources\LeagueResource;
use App\Http\Resources\LeagueWithProgressResource;
use App\Models\League;
use App\Models\Team;
use App\Services\League\DTO\StoreLeagueDTO;
use App\Services\League\LeagueService;
use Illuminate\Http\JsonResponse;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LeagueController extends ApiController
{
    public function store(StoreLeagueRequest $request, LeagueService $service): JsonResponse
    {
        if (!Team::query()->exists()) {
            return $this->errorResponse('No teams present');
        }

        try {
            $dto = StoreLeagueDTO::fromRequest($request);
        } catch (UnknownProperties) {
            return $this->errorResponse('Your data is invalid');
        }

        $request->validate($service->getStoreValidationRules($dto));

        $league = $service->storeLeague($dto);

        if (is_null($league)) {
            return $this->errorResponse('Error while storing new league');
        }

        return $this->okResponse(LeagueResource::make($league));
    }

    public function show(League $league): JsonResponse
    {
        return $this->okResponse(LeagueResource::make(
            $league->load(
                [
                    'matches' => function ($query) {
                        $query->with('awayTeam', 'houseTeam');
                    },
                    'teams',
                ]
            ),
        ));
    }

    public function showProgress(League $league): JsonResponse
    {
        $currentWeek = $league->progress_week;

        if (is_null($currentWeek)) {
            throw new NotFoundHttpException();
        }

        return $this->okResponse(LeagueWithProgressResource::make(
            $league->load(
                [
                    'matches' => function ($query) use ($currentWeek) {
                        $query->where('week', $currentWeek)
                            ->with('awayTeam', 'houseTeam');
                    },
                    'teams',
                ]
            ),
        ));
    }

    public function progress(League $league, LeagueService $service): JsonResponse
    {
        if ($league->progress_week === $league->total_weeks) {
            throw new BadRequestHttpException('No weeks to progress');
        }

        $success = $service->setLeague($league)
            ->progressLeague();

        if (!$success) {
            return $this->errorResponse('Unexpected error occurred');
        }

        return $this->okResponse();
    }

    public function progressAll(League $league, LeagueService $service): JsonResponse
    {
        if ($league->progress_week === $league->total_weeks) {
            throw new BadRequestHttpException('No weeks to progress');
        }

        $success = $service->setLeague($league)
            ->progressWholeLeague();

        if (!$success) {
            return $this->errorResponse('Unexpected error occurred');
        }

        return $this->okResponse();
    }

    public function reset(League $league, LeagueService $service): JsonResponse
    {
        $success = $service->setLeague($league)
            ->resetLeague();

        if (!$success) {
            return $this->errorResponse('Unexpected error occurred');
        }

        return $this->okResponse();
    }
}
