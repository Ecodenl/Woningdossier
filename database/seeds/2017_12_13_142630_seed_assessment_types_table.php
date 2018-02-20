<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedAssessmentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            'Calculated design',
            'Calculated built',
            'Calculated actual',
            'Calculated tailored',
            'Measured actual',
            'Measured corrected climate',
            'Measured corrected for use',
            'Measured standard',

        ];

        foreach ($types as $type) {
            DB::table('assessment_types')->insert([
                    ['name' => $type],
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            //
        });
    }
}
