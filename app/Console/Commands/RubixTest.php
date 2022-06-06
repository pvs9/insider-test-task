<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rubix\ML\Classifiers\NaiveBayes;
use Rubix\ML\CrossValidation\Metrics\Accuracy;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Extractors\CSV;
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Transformers\NumericStringConverter;

class RubixTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rubix:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Rubix ML model accuracy';

    public function handle(): void
    {
        $houseDataset = Labeled::fromIterator(new CSV(storage_path('app/datasets/prepared/house.csv'), true))
            ->randomize()->take(25);

        $estimator = PersistentModel::load(new Filesystem(storage_path('app/datasets/prepared/house_model.rbx')));

        $predictions = $estimator->predict($houseDataset);
        $metric = new Accuracy();

        $this->line((string)$metric->score($predictions, $houseDataset->labels()));
    }
}
