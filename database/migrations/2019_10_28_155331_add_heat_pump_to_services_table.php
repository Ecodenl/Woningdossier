<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHeatPumpToServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::table('steps')->where('short', 'heat-pump')->first() instanceof stdClass) {


            $services = [
                [
                    'names' => [
                        'nl' => 'Warmtepomp',
                    ],
                    'short' => 'heat-pump',
                    'service_type' => 'Heating',
                    'order' => 0,
                    'info' => [
                        'nl' => 'Hier kunt u aangeven of u in de huidige situatie in plaats van een cv-ketel een warmtepomp als enige warmteopwekker in huis hebt. U hebt de keuze uit een warmtepomp met buitenlucht of bodemenergie als warmtebron.',
                    ],
                    'service_values' => [
                        [
                            'values' => [
                                'nl' => 'Geen',
                            ],
                            'order' => 1,
                            'calculate_value' => 1,
                        ],
                        [
                            'values' => [
                                'nl' => 'Volledige warmtepomp buitenlucht',
                            ],
                            'order' => 2,
                            'calculate_value' => 2,
                        ],
                        [
                            'values' => [
                                'nl' => 'Volledige warmtepomp bodem',
                            ],
                            'order' => 3,
                            'calculate_value' => 3,
                        ],
                        [
                            'values' => [
                                'nl' => 'Hybride warmtepomp',
                            ],
                            'order' => 4,
                            'calculate_value' => 4,
                        ],
                        [
                            'values' => [
                                'nl' => 'Collectieve warmtepomp',
                            ],
                            'order' => 5,
                            'calculate_value' => 5,
                        ],
                    ],
                ],
            ];

            foreach ($services as $service) {
                $uuid = \App\Helpers\Str::uuid();
                foreach ($service['names'] as $locale => $name) {
                    \DB::table('translations')->insert([
                        'key'         => $uuid,
                        'language'    => $locale,
                        'translation' => $name,
                    ]);
                }

                $infoUuid = \App\Helpers\Str::uuid();
                foreach ($service['info'] as $locale => $name) {
                    \DB::table('translations')->insert([
                        'key'         => $infoUuid,
                        'language'    => $locale,
                        'translation' => $name,
                    ]);
                }

                $nameUuid = \DB::table('translations')
                    ->where('translation', $service['service_type'])
                    ->where('language', 'en')
                    ->first(['key']);

                // Get the category. If it doesn't exist: create it
                $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();

                if ($serviceType instanceof \stdClass) {
                    $serviceId = \DB::table('services')->insertGetId([
                        'name'            => $uuid,
                        'short' => $service['short'],
                        'service_type_id' => $serviceType->id,
                        'order' => $service['order'],
                        'info' => $infoUuid,
                    ]);

                    foreach ($service['service_values'] as $serviceValue) {
                        $uuid = \App\Helpers\Str::uuid();
                        foreach ($serviceValue['values'] as $locale => $name) {
                            \DB::table('translations')->insert([
                                'key'         => $uuid,
                                'language'    => $locale,
                                'translation' => $name,
                            ]);
                        }

                        \DB::table('service_values')->insert([
                            'service_id' => $serviceId,
                            'value' => $uuid,
                            'order' => $serviceValue['order'],
                            'calculate_value' => isset($serviceValue['calculate_value']) ? $serviceValue['calculate_value'] : null,
                            'is_default' => $serviceValue['is_default'] ?? false,
                        ]);
                    }
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
