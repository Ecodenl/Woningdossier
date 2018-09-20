<?php

use Illuminate\Database\Migrations\Migration;

class DeleteSleepingRoomWindowsFromElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // so we can get the id and remove it later on
        $sleepingRoomWindow = DB::table('elements')->where('short', 'sleeping-rooms-windows');

        if ($sleepingRoomWindow->first() instanceof stdClass) {
            // remove the user interests for the sleeping rooms windows
            DB::table('user_interests')
                ->where('interested_in_type', 'element')
                ->where('interested_in_id', $sleepingRoomWindow->first()->id)
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add the max savings back
        $maxSavings = [
            'Vrijstaande woning' => [
                'Ramen in de slaapruimtes' => 15,
            ],
            '2 onder 1 kap' => [
                'Ramen in de slaapruimtes' => 20,
            ],
            'Hoekwoning' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Tussenwoning' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Benedenwoning hoek' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Benedenwoning tussen' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Bovenwoning hoek' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Bovenwoning tussen' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Appartement tussen op een tussenverdieping' => [
                'Ramen in de slaapruimtes' => 25,
            ],
            'Appartement hoek op een tussenverdieping' => [
                'Ramen in de slaapruimtes' => 25,
            ],
        ];

        $elements = [
            [
                'names' => [
                    'nl' => 'Ramen in de slaapruimtes',
                ],
                'short' => 'sleeping-rooms-windows',
                'service_type' => 'Heating',
                'order' => 1,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'element_values' => [
                    [
                        'values' => [
                            'nl' => 'Enkelglas',
                        ],
                        'order' => 0,
                    ],
                    [
                        'values' => [
                            'nl' => 'Dubbelglas',
                        ],
                        'order' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'HR++ glas',
                        ],
                        'order' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Drievoudige beglazing',
                        ],
                        'order' => 3,
                    ],
                ],
            ],
        ];

        foreach ($elements as $element) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($element['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            $infoUuid = \App\Helpers\Str::uuid();
            foreach ($element['info'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $infoUuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            $nameUuid = \DB::table('translations')
                ->where('translation', $element['service_type'])
                ->where('language', 'en')
                ->first(['key']);

            // Get the category. If it doesn't exist: create it
            $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();

            if ($serviceType instanceof \stdClass) {
                $elementId = \DB::table('elements')->insertGetId([
                    'name'            => $uuid,
                    'short' => $element['short'],
                    'service_type_id' => $serviceType->id,
                    'order' => $element['order'],
                    'info' => $infoUuid,
                ]);

                foreach ($element['element_values'] as $elementValue) {
                    $uuid = \App\Helpers\Str::uuid();
                    foreach ($elementValue['values'] as $locale => $name) {
                        \DB::table('translations')->insert([
                            'key'         => $uuid,
                            'language'    => $locale,
                            'translation' => $name,
                        ]);
                    }

                    \DB::table('element_values')->insert([
                        'element_id' => $elementId,
                        'value' => $uuid,
                        'order' => $elementValue['order'],
                        'calculate_value' => isset($elementValue['calculate_value']) ? $elementValue['calculate_value'] : null,
                    ]);
                }
            }
        }

        foreach ($maxSavings as $buildingType => $elements) {
            $bt = \DB::table('building_types')->leftJoin('translations', 'building_types.name', '=', 'translations.key')
                ->where('language', 'nl')
                ->where('translation', $buildingType)
                ->first(['building_types.id']);

            $buildingTypeId = $bt->id;

            foreach ($elements as $element => $percentage) {
                $el = \DB::table('elements')->leftJoin('translations', 'elements.name', '=', 'translations.key')
                    ->where('language', 'nl')
                    ->where('translation', $element)
                    ->first(['elements.id']);

                $elementId = $el->id;

                \DB::table('building_type_element_max_savings')->insert([
                    'building_type_id' => $buildingTypeId,
                    'element_id' => $elementId,
                    'max_saving' => $percentage,
                ]);
            }
        }
    }
}
