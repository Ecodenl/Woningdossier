<?php

use Illuminate\Database\Seeder;

class SubStepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $generalData = DB::table('steps')->where('slug', 'general-data')->first();
        $subSteps = [
            [
                'names' => [
                    'nl' => 'Gebouwkenmerken',
                ],
                'slug' => 'gebouw-kenmerken',
                'short' => 'building-characteristics',
                'step_id' => $generalData->id,
                'order' => 1,
            ],
            [
                'names' => [
                    'nl' => 'Huidige staat',
                ],
                'slug' => 'huidige-staat',
                'short' => 'current-state',
                'step_id' => $generalData->id,
                'order' => 2,
            ],
            [
                'names' => [
                    'nl' => 'Gebruik',
                ],
                'slug' => 'gebruik',
                'short' => 'usage',
                'step_id' => $generalData->id,
                'order' => 3,
            ],
            [
                'names' => [
                    'nl' => 'Interesse',
                ],
                'slug' => 'interesse',
                'short' => 'interest',
                'step_id' => $generalData->id,
                'order' => 4,
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

            if (! DB::table('sub_steps')->where('short', $subStep['slug'])->first() instanceof stdClass) {
                \DB::table('sub_steps')->insert([
                    'name' => $uuid,
                    'step_id' => $subStep['step_id'],
                    'slug' => $subStep['slug'],
                    'short' => $subStep['short'],
                    'order' => $subStep['order'],
                ]);
            }
        }
    }
}
