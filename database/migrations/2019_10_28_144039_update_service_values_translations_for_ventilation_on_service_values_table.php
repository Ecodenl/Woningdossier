<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateServiceValuesTranslationsForVentilationOnServiceValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $serviceValues = DB::table('service_values')->where('service_id', 6)->get();

        foreach ($serviceValues as $serviceValue) {
            switch ($serviceValue->calculate_value) {
                case 1: {
                    DB::table('translations')->where('key', $serviceValue->value)->update([
                        'translation' => 'Natuurlijke ventilatie'
                    ]);
                    break;
                }
                case 2: {
                     DB::table('translations')->where('key', $serviceValue->value)->update([
                         'translation' => 'Mechanische ventilatie'
                     ]);
                    break;
                }
                case 3: {
                     DB::table('translations')->where('key', $serviceValue->value)->update([
                         'translation' => 'Gebalanceerde ventilatie'
                     ]);
                    break;
                }
                case 4: {
                     DB::table('translations')->where('key', $serviceValue->value)->update([
                         'translation' => 'Decentrale mechanische ventilatie'
                     ]);
                    break;
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
        $serviceValues = DB::table('service_values')->where('service_id', 6)->get();

        foreach ($serviceValues as $serviceValue) {
            switch ($serviceValue->calculate_value) {
                case 1: {
                    DB::table('translations')->where('key', $serviceValue->value)->update([
                        'translation' => 'Natuurlijk'
                    ]);
                    break;
                }
                case 2: {
                    DB::table('translations')->where('key', $serviceValue->value)->update([
                        'translation' => 'Mechanisch'
                    ]);
                    break;
                }
                case 3: {
                    DB::table('translations')->where('key', $serviceValue->value)->update([
                        'translation' => 'Gebalanceerd'
                    ]);
                    break;
                }
                case 4: {
                    DB::table('translations')->where('key', $serviceValue->value)->update([
                        'translation' => 'Decentraal mechanisch'
                    ]);
                    break;
                }
            }
        }
    }
}
