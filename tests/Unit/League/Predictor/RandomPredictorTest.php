<?php

use App\Models\Team;
use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\Predictor\RandomPredictor;

it('predicts random numbers from 0 to 3 in any case', function (Team $team1, Team $team2) {
    $predictor = new RandomPredictor();
    $results = $predictor->predictMatch(new LeagueMatchPredictionDTO(
        houseTeam: $team1->toArray(),
        awayTeam: $team2->toArray()
    ));

    expect($results->housePoints)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(0)
        ->toBeLessThanOrEqual(3);
    expect($results->awayPoints)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(0)
        ->toBeLessThanOrEqual(3);
})->with('match-predictor-teams');
