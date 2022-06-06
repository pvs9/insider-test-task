<?php

use App\Models\League;
use App\Models\Team;
use App\Services\League\Enums\LeagueTypeEnum;
use App\Services\League\Implementations\InsiderLeague;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    Team::factory()->count(6)->create();
});

it('stores league with correct request', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    assertDatabaseHas(
        'leagues',
        [
           'id' => $response->json('data.id')
        ]
    );
});

it('stores league with custom teams', function () {
    $newTeams = Team::factory()->count(4)->create();

    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value,
            'teams' => $newTeams->toArray()
        ]
    )->assertOk();
    $leagueId = $response->json('data.id');

    foreach ($newTeams as $newTeam) {
        assertDatabaseHas(
            'league_team',
            [
                'league_id' => $leagueId,
                'team_id' => $newTeam->id,
            ]
        );
    }
});

it('does not store league with incorrect type', function () {
    post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => 888
        ],
        [
            'Accept' => 'application/json'
        ]
    )->assertStatus(422);
});

it('does not store league when no teams are present', function () {
    Team::all()->each->delete();

    post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => 888
        ],
        [
            'Accept' => 'application/json'
        ]
    )->assertStatus(422);
});

it('shows league', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    $response = get(route('league.show.main', ['league' => $response->json('data.id')]))->assertOk();

    expect($response->json('data'))
        ->toHaveKeys(['id', 'name', 'matches', 'teams']);
});

it('does not show progress before progress applied', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    get(
        route('league.show.progress.show', ['league' => $response->json('data.id')]),
    )->assertNotFound();
});

it('does progress for the first time', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    post(
        route('league.show.progress.week', ['league' => $response->json('data.id')]),
    )->assertOk();

    assertDatabaseMissing(
        'league_matches',
        [
            'league_id' => $response->json('data.id'),
            'week' => 1,
            'house_points' => null,
            'away_points' => null,
        ]
    );
});

it('does progress till the last week but not further', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    $weeks = (new InsiderLeague())->getWeeksQuantity();

    for ($i = 1; $i <= $weeks; $i++) {
        post(
            route('league.show.progress.week', ['league' => $response->json('data.id')]),
        )->assertOk();
    }

    post(
        route('league.show.progress.week', ['league' => $response->json('data.id')]),
    )->assertStatus(400);
});

it('does progress whole league for the first time but not more', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    post(
        route('league.show.progress.all', ['league' => $response->json('data.id')]),
    )->assertOk();

    assertDatabaseMissing(
        'league_matches',
        [
            'league_id' => $response->json('data.id'),
            'house_points' => null,
            'away_points' => null,
        ]
    );

    post(
        route('league.show.progress.all', ['league' => $response->json('data.id')]),
    )->assertStatus(400);
});

it('does reset league', function () {
    $response = post(
        route('league.store'),
        [
            'name' => 'Test League Name',
            'type' => LeagueTypeEnum::INSIDER_LEAGUE->value
        ]
    )->assertOk();

    post(
        route('league.show.reset', ['league' => $response->json('data.id')]),
    )->assertOk();

    /** @var League $league */
    $league = League::query()->find($response->json('data.id'));

    expect($league->matches->pluck('away_points'))
        ->each
        ->toEqual(0)
        ->and($league->matches->pluck('house_points'))
        ->each
        ->toEqual(0);
});
