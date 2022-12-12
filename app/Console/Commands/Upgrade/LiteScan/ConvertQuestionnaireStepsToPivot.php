<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertQuestionnaireStepsToPivot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:convert-questionnaire-steps-to-pivot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert questionnaire step id to pivot.';

    public function handle()
    {
        // If done, no need to do again
        if (Schema::hasColumn('questionnaires', 'step_id')) {
            $oldHeatingSteps = DB::table('steps')->whereIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater'])->pluck('id')->toArray();
            $heatingStep = DB::table('steps')->where('short', 'heating')->first('id')->id;

            DB::table('questionnaires')
                ->orderBy('id')
                ->chunk(100, function ($questionnaires) use ($oldHeatingSteps, $heatingStep) {
                    foreach ($questionnaires as $questionnaire) {
                        $stepId = $questionnaire->step_id;
                        if (in_array($stepId, $oldHeatingSteps)) {
                            $stepId = $heatingStep;
                        }

                        DB::table('questionnaire_step')->insert([
                            'questionnaire_id' => $questionnaire->id,
                            'step_id' => $stepId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });

            Schema::table('questionnaires', function (Blueprint $table) {
                $table->dropForeign(['step_id']);
                $table->dropColumn('step_id');
            });
        }
    }
}