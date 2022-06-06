<?php

use App\Models\Team;
use App\Services\League\TeamsDrafter\RandomTeamsDrafter;

beforeEach(function () {
    Team::factory()->count(6)->create();
});

it('drafts teams in random order', function () {
    $teamsDrafter = new RandomTeamsDrafter();
    $draft1 = $teamsDrafter->draft(6);
    $draft2 = $teamsDrafter->draft(6);

    expect([$draft1, $draft2])
        ->each
        ->toBeCollection();

    expect($draft1->pluck('id')->all())
        ->not()
        ->toMatchArray($draft2->pluck('id')->all());
});
