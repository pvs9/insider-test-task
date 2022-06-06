<?php

use App\Models\Team;
use App\Services\League\DTO\LeagueMatchPredictionDTO;
use App\Services\League\Predictor\RubixMLPredictor;

it('predicts integer scores in any case', function (Team $team1, Team $team2) {
    $predictor = new RubixMLPredictor();
    $results = $predictor->predictMatch(new LeagueMatchPredictionDTO(
        houseTeam: $team1->toArray(),
        awayTeam: $team2->toArray()
    ));

    expect($results->housePoints)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(0);
    expect($results->awayPoints)
        ->toBeInt()
        ->toBeGreaterThanOrEqual(0);
})->with('match-predictor-teams');
