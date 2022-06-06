<?php

namespace App\Providers;

use App\Services\League\ChampionshipPredictor\AverageEfficiencyChampionshipPredictor;
use App\Services\League\ChampionshipPredictor\ChampionshipPredictor;
use App\Services\League\LeagueService;
use App\Services\League\Predictor\Predictor;
use App\Services\League\Predictor\RubixMLPredictor;
use App\Services\League\TeamsDrafter\RandomTeamsDrafter;
use App\Services\League\TeamsDrafter\TeamsDrafter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(ChampionshipPredictor::class, AverageEfficiencyChampionshipPredictor::class);
        $this->app->singleton(Predictor::class, RubixMLPredictor::class);
        $this->app->singleton(TeamsDrafter::class, RandomTeamsDrafter::class);
        $this->app->singleton(LeagueService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
