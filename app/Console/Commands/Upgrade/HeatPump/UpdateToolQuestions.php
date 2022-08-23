<?php

namespace App\Console\Commands\Upgrade\HeatPump;

use App\Models\ToolQuestion;
use Illuminate\Console\Command;

class UpdateToolQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:update-tool-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the tool questions in the format for the heat pump, this includes "hardcoded" updates on translations.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Artisan::call('db:seed', ['--class' => \ScansTableSeeder::class]);
        \Artisan::call('db:seed', ['--class' => \StepsTableSeeder::class]);
        \Artisan::call('db:seed', ['--class' => \ToolQuestionsTableSeeder::class]);
        \Artisan::call('db:seed', ['--class' => \SubStepsTableSeeder::class]);

        ToolQuestion::findByShort('heat-source')->update(['name' => ['nl' => 'Wat wordt er gebruikt voor verwarming']]);

        $heatPumpType = ToolQuestion::findByShort('heat-pump-type');
        $heatPumpType->update(['name' => ['nl' => 'Wat voor type warmtepomp is er?']]);

        $heatPumpValues = [
            'Hybride warmtepomp met buitenlucht',
            'Hybride warmtepomp met ventilatielucht',
            'Hybride warmtepomp met pvt panelen',
            'Volledige warmtepomp met buitenlucht',
            'Volledige warmpteomp met bodemwarmte',
            'Volledige warmpteomp met pvt panelen',
            'Warmtempompboiler'
        ];
    }
}
