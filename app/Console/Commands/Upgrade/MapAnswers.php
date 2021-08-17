<?php

namespace App\Console\Commands\Upgrade;

use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\UserEnergyHabit;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MapAnswers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:map-answers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map current data to new formats, eg; cook_gas was a boolean will now be a electric, induction or gas field ';

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

        $userEnergyHabits = UserEnergyHabit::allInputSources()
            ->limit(500)
            ->whereHas('user.building')
            ->with('user.building')
            ->get();

        $this->info("Mapping user energy habits...");
        $this->info('Cook gas field to the tool question answers...');

        $bar = $this->output->createProgressBar($userEnergyHabits->count());
        $bar->start();

        foreach ($userEnergyHabits as $userEnergyHabit) {

            $toolQuestion = ToolQuestion::findByShort('cook-type');

            $cookGas = $userEnergyHabit->cook_gas;

            $data = [
                'tool_question_id' => $toolQuestion->id,
                'input_source_id' => $userEnergyHabit->input_source_id,
                'building_id' => $userEnergyHabit->user->building->id
            ];

            // now map the actual answer.
            if ($cookGas == 1) {
                $answer = 'gas';
            } else {
                $answer = 'electric';
            }

            $data['tool_question_custom_value_id'] = ToolQuestionCustomValue::findByShort($answer)->id;
            $data['answer'] = $answer;

            DB::table('tool_question_answers')->insert($data);

            $bar->advance();
        }
        $bar->finish();
    }
}
