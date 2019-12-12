<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubStepsToStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $generalData = DB::table('steps')->where('short', 'general-data')->first();

        if ($generalData instanceof stdClass) {

            $subSteps = [
                [
                    'names' => [
                        'nl' => 'Gebouwkenmerken',
                    ],
                    'slug' => 'gebouw-kenmerken',
                    'short' => 'building-characteristics',
                    'parent_id' => $generalData->id,
                    'order' => 0,
                ],
                [
                    'names' => [
                        'nl' => 'Huidige staat',
                    ],
                    'slug' => 'huidige-staat',
                    'short' => 'current-state',
                    'parent_id' => $generalData->id,
                    'order' => 1,
                ],
                [
                    'names' => [
                        'nl' => 'Gebruik',
                    ],
                    'slug' => 'gebruik',
                    'short' => 'usage',
                    'parent_id' => $generalData->id,
                    'order' => 2,
                ],
                [
                    'names' => [
                        'nl' => 'Interesse',
                    ],
                    'slug' => 'interesse',
                    'short' => 'interest',
                    'parent_id' => $generalData->id,
                    'order' => 3,
                ],
            ];

            foreach ($subSteps as $subStep) {
                $uuid = \App\Helpers\Str::uuid();
                foreach ($subStep['names'] as $locale => $names) {
                    \DB::table('translations')->insert([
                        'key'         => $uuid,
                        'language'    => $locale,
                        'translation' => $names,
                    ]);
                }

                if (! DB::table('steps')->where('short', $subStep['slug'])->first() instanceof stdClass) {
                    \DB::table('steps')->insert([
                        'name' => $uuid,
                        'parent_id' => $subStep['parent_id'],
                        'slug' => $subStep['slug'],
                        'short' => $subStep['short'],
                        'order' => $subStep['order'],
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
