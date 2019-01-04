<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortToRoofTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    if ( ! Schema::hasColumn( 'roof_types', 'short' ) ) {

	        Schema::table( 'roof_types', function ( Blueprint $table ) {
			    $table->string( 'short' )->after( 'name' );
		    } );
        }

        $roofTypes = [
            [
                'calculate_value' => 1,
                'short' => 'pitched',
            ],
            [
                'calculate_value' => 3,
                'short' => 'flat',
            ],
            [
                'calculate_value' => 5,
                'short' => 'none',
            ],
        ];

        foreach ($roofTypes as $roofType) {
            $roofTypeResult = DB::table('roof_types')->where('calculate_value', $roofType['calculate_value'])->first();
            if ($roofTypeResult instanceof stdClass) {

                unset($roofType['names']);
                DB::table('roof_types')
                    ->where('calculate_value', $roofType['calculate_value'])
                    ->update($roofType);
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
        Schema::table('roof_types', function(Blueprint $table){
        	$table->dropColumn('short');
        });
    }
}
