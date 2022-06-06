<?php

use App\Models\Team;

dataset('match-predictor-teams', function () {
    return [
        [
            fn() => Team::factory()->create(),
            fn() => Team::factory()->create(),
        ],
        [
            fn() => Team::factory()->create(['rating' => 0]),
            fn() => Team::factory()->create(['rating' => 99]),
        ],
        [
            fn() => Team::factory()->create(['name' => 'Test Team Name 1']),
            fn() => Team::factory()->create(['name' => 'Test Team Name 2']),
        ],
    ];
});
