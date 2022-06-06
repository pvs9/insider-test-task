<?php

use App\Http\Controllers\Api\LeagueController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('league')->name('league.')->group(function () {
    Route::post('/', [LeagueController::class, 'store'])->name('store');

    Route::prefix('/{league}')->name('show.')->group(function () {
        Route::get('/', [LeagueController::class, 'show'])->name('main');
        Route::post('/reset', [LeagueController::class, 'reset'])->name('reset');

        Route::prefix('/progress')->name('progress.')->group(function () {
            Route::get('/', [LeagueController::class, 'showProgress'])
                ->name('show');
            Route::post('/', [LeagueController::class, 'progress'])
                ->name('week');
            Route::post('/all', [LeagueController::class, 'progressAll'])
                ->name('all');
        });
    });
});

Route::prefix('team')->name('team.')->group(function () {
    Route::get('/', [TeamController::class, 'all'])->name('all');
});
