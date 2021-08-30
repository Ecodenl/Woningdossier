<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;

class ConvertExampleBuildingContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:convert-example-building-contents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the example building contents to the new structure';

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
        $this->info("user_energy_habits.cook_gas -> tool_question_answers.cook-type");

        $this->info("building_features.building_heating_application_id -> tool_question_answers.building-heating-application");

        $this->info("boiler: als service.4 een service value heeft -> tool_question_answers.heat-source = hr-boiler");

        $this->info("heat-pump: service.8 ");
    }
}
