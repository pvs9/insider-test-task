<?php

use App\Models\Team;
use App\Services\League\Implementations\InsiderLeague;

it('returns correct teams and weeks quantity for implementation', function () {
    $implementation = new InsiderLeague();
    $teamsQuantity = $implementation->getTeamsQuantity();
    $weeksQuantity = $implementation->getWeeksQuantity();

    expect([$teamsQuantity, $weeksQuantity])
        ->each
        ->toBeInt()
        ->and([$teamsQuantity % 2, $weeksQuantity % 2])
        ->each
        ->toEqual(0)
        ->and($weeksQuantity)
        ->toEqual($teamsQuantity * 2 - 2);
});

it('returns correct win, draw and loss points for implementation', function () {
    $implementation = new InsiderLeague();

    expect($implementation->getWinPoints())
        ->toEqual(3)
        ->and($implementation->getDrawPoints())
        ->toEqual(1)
        ->and($implementation->getLossPoints())
        ->toEqual(0);
});

it('returns correct validation rules for implementation', function () {
    $implementation = new InsiderLeague();
    $rules = $implementation->getValidationRules();

    expect($rules)
        ->toBeArray()
        ->toHaveKey('teams')
        ->and($rules['teams'])
        ->toContain('array', 'min:4');
});

it('generates correct fixtures according to round-robin algo', function () {
    $implementation = new InsiderLeague();
    $teams = Team::factory()->count(4)->create();
    $teamIds = $teams->pluck('id')->all();

    $matches = $implementation->getFixtures($teams)
        ->sortBy([
            ['week', 'asc'],
            ['id', 'asc'],
        ]);

    /** First team is house round-robin cycle */
    expect($matches->get(0))
        ->toHaveKey('house_team_id', $teamIds[0])
        ->toHaveKey('away_team_id', $teamIds[2])
        ->and($matches->get(1))
        ->toHaveKey('house_team_id', $teamIds[1])
        ->toHaveKey('away_team_id', $teamIds[3])
        ->and($matches->get(2))
        ->toHaveKey('house_team_id', $teamIds[0])
        ->toHaveKey('away_team_id', $teamIds[1])
        ->and($matches->get(3))
        ->toHaveKey('house_team_id', $teamIds[3])
        ->toHaveKey('away_team_id', $teamIds[2])
        ->and($matches->get(4))
        ->toHaveKey('house_team_id', $teamIds[0])
        ->toHaveKey('away_team_id', $teamIds[3])
        ->and($matches->get(5))
        ->toHaveKey('house_team_id', $teamIds[2])
        ->toHaveKey('away_team_id', $teamIds[1]);

    /** First team is away round-robin cycle */
    expect($matches->get(6))
        ->toHaveKey('away_team_id', $teamIds[0])
        ->toHaveKey('house_team_id', $teamIds[2])
        ->and($matches->get(7))
        ->toHaveKey('away_team_id', $teamIds[1])
        ->toHaveKey('house_team_id', $teamIds[3])
        ->and($matches->get(8))
        ->toHaveKey('away_team_id', $teamIds[0])
        ->toHaveKey('house_team_id', $teamIds[1])
        ->and($matches->get(9))
        ->toHaveKey('away_team_id', $teamIds[3])
        ->toHaveKey('house_team_id', $teamIds[2])
        ->and($matches->get(10))
        ->toHaveKey('away_team_id', $teamIds[0])
        ->toHaveKey('house_team_id', $teamIds[3])
        ->and($matches->get(11))
        ->toHaveKey('away_team_id', $teamIds[2])
        ->toHaveKey('house_team_id', $teamIds[1]);
});
