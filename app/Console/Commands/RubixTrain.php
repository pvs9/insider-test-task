<?php

namespace App\Console\Commands;

use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Rubix\ML\Classifiers\ClassificationTree;
use Rubix\ML\Classifiers\RandomForest;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

class RubixTrain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rubix:train';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train Rubix ML prediction module';

    public function handle(): void
    {
        $datasetFiles = Storage::files('datasets');
        $teams = Team::all();
        $houseRows = [
            ['HouseTeam', 'AwayTeam', 'HouseTeamRating', 'AwayTeamRating', 'HouseTeamScore']
        ];
        $awayRows = [
            ['HouseTeam', 'AwayTeam', 'HouseTeamRating', 'AwayTeamRating', 'AwayTeamScore']
        ];

        foreach ($datasetFiles as $datasetFile) {
            $file = Storage::readStream($datasetFile);

            while ($match = fgetcsv($file, 1024)) {
                if ($match[0] === 'Div') {
                    continue;
                }

                $neededTeams = $teams
                    ->whereIn('name', [$match[3], $match[4]])
                    ->pluck('rating', 'name');

                $houseRows[] = [
                    $match[3],
                    $match[4],
                    $neededTeams->get($match[3], 0),
                    $neededTeams->get($match[4], 0),
                    $match[5]
                ];

                $awayRows[] = [
                    $match[3],
                    $match[4],
                    $neededTeams->get($match[3], 0),
                    $neededTeams->get($match[4], 0),
                    $match[6]
                ];
            }
        }

        $houseDataset = fopen(storage_path('app/datasets/prepared/house.csv'), 'wb');

        foreach ($houseRows as $row) {
            fputcsv($houseDataset, $row);
        }

        fclose($houseDataset);

        $awayDataset = fopen(storage_path('app/datasets/prepared/away.csv'), 'wb');

        foreach ($awayRows as $row) {
            fputcsv($awayDataset, $row);
        }

        fclose($awayDataset);

        $houseDataset = Labeled::fromIterator(new CSV(storage_path('app/datasets/prepared/house.csv'), true));
        $estimator = new PersistentModel(
            new RandomForest(new ClassificationTree(15), 800, 0.2, true),
            new Filesystem(storage_path('app/datasets/prepared/house_model.rbx'))
        );

        $estimator->train($houseDataset);
        $estimator->save();

        $awayDataset = Labeled::fromIterator(new CSV(storage_path('app/datasets/prepared/away.csv'), true));
        $estimator = new PersistentModel(
            new RandomForest(new ClassificationTree(15), 800, 0.2, true),
            new Filesystem(storage_path('app/datasets/prepared/away_model.rbx'))
        );

        $estimator->train($awayDataset);
        $estimator->save();
    }
}
