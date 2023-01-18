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
    protected $signature = 'upgrade:lite-scan:convert-questionnaire-steps-to-pivot 
    {--rollback : if enabled, we will convert backwards.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert questionnaire step id to pivot.';

    public function handle()
    {
        // Mostly for development purposes
        if ($this->option('rollback')) {
            if (! Schema::hasColumn('questionnaires', 'step_id')) {
                // Can't 100% rollback but that's okay
                Schema::table('questionnaires', function (Blueprint $table) {
                    $table->integer('step_id')->unsigned()->nullable()->after('name');
                    $table->foreign('step_id')->references('id')->on('steps')->onDelete('set null');
                    $table->integer('order')->after('cooperation_id')->default(0);
                });

                DB::table('questionnaire_step')
                    ->orderBy('id')
                    ->chunk(100, function ($questionnaires) {
                        foreach ($questionnaires as $questionnaire) {
                            DB::table('questionnaires')->where('id', $questionnaire->questionnaire_id)
                                ->update([
                                'step_id' => $questionnaire->step_id,
                                'order' => $questionnaire->order,
                            ]);
                        }
                    });

                DB::table('questionnaire_step')->truncate();
            }
        } else {
            // If done, no need to do again
            if (Schema::hasColumn('questionnaires', 'step_id')) {
                $oldHeatingSteps = DB::table('steps')->whereIn('short', ['high-efficiency-boiler', 'heat-pump', 'heater'])->pluck('id')->toArray();
                $heatingStep = DB::table('steps')->where('short', 'heating')->first('id')->id;

                DB::table('questionnaires')
                    ->orderBy('id')
                    ->whereNotNull('step_id')
                    ->chunk(100, function ($questionnaires) use ($oldHeatingSteps, $heatingStep) {
                        foreach ($questionnaires as $questionnaire) {
                            $stepId = $questionnaire->step_id;
                            if (in_array($stepId, $oldHeatingSteps)) {
                                $stepId = $heatingStep;
                            }

                            DB::table('questionnaire_step')->insert([
                                'questionnaire_id' => $questionnaire->id,
                                'step_id' => $stepId,
                                'order' => $questionnaire->order,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    });

                Schema::table('questionnaires', function (Blueprint $table) {
                    $table->dropForeign(['step_id']);
                    $table->dropColumn('step_id');
                    $table->dropColumn('order');
                });
            }
        }
    }
}