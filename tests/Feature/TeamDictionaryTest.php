<?php

use App\Models\Team;

use function Pest\Laravel\get;

it('provides correct dictionary for teams', function () {
    $response = get(
        route('team.all'),
    )->assertOk();

    expect($response->json('data'))
        ->toHaveCount(0);

    Team::factory()->count(4)->create();

    $response = get(
        route('team.all'),
    )->assertOk();

    expect($response->json('data'))
        ->toHaveCount(4);
});
