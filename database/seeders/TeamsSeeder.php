<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Team::query()->insert(
            [
                ['name' => 'Man City', 'rating' => 71],
                ['name' => 'Liverpool', 'rating' => 70],
                ['name' => 'Chelsea', 'rating' => 69],
                ['name' => 'Tottenham', 'rating' => 68],
                ['name' => 'West Ham', 'rating' => 67],
                ['name' => 'Arsenal', 'rating' => 67],
                ['name' => 'Man United', 'rating' => 67],
                ['name' => 'Crystal Palace', 'rating' => 67],
                ['name' => 'Leicester', 'rating' => 67],
                ['name' => 'Brighton', 'rating' => 67],
                ['name' => 'Wolverhampton', 'rating' => 66],
                ['name' => 'Aston Villa', 'rating' => 66],
                ['name' => 'Burnley', 'rating' => 66],
                ['name' => 'Brentford', 'rating' => 66],
                ['name' => 'Southampton', 'rating' => 66],
                ['name' => 'Newcastle', 'rating' => 66],
                ['name' => 'Everton', 'rating' => 65],
                ['name' => 'Leeds', 'rating' => 65],
                ['name' => 'Watford', 'rating' => 65],
                ['name' => 'Norwich', 'rating' => 64],
                ['name' => 'Fulham', 'rating' => 66],
                ['name' => 'Sheffield United', 'rating' => 64],
                ['name' => 'West Bromwich Albion', 'rating' => 65],
            ]
        );
    }
}
