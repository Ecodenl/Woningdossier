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

	    $updates = [
	    	[
	    		'attributes' => [ 'calculate_value' => 1, ],
			    'values' => [ 'short' => 'pitched', ],
		    ],
		    [
			    'attributes' => [ 'calculate_value' => 3, ],
			    'values' => [ 'short' => 'flat', ],
		    ],
		    [
			    'attributes' => [ 'calculate_value' => 5, ],
			    'values' => [ 'short' => 'none', ],
		    ],
	    ];
	    foreach($updates as $update){
		    DB::table('roof_types')->updateOrInsert($update['attributes'], $update['values']);
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
