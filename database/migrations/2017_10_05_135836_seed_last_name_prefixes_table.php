<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedLastNamePrefixesTable extends Migration {

	protected $prefixes = [
		'van',
		'de',
		'van der',
		'van de',
		'van den',
		'den',
		'ten',
		'ter',
		'te',
		'van \'t',
		'el',
		'le',
		'van het',
		'in \'t',
		'\'t',
		'von',
		'du',
		'da',
		'de la',
		'la',
		'der',
	];

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		foreach ( $this->prefixes as $prefix ) {
			\App\Models\LastNamePrefix::create( [ 'name' => $prefix ]);
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		foreach ( $this->prefixes as $prefix ) {
			\App\Models\LastNamePrefix::where( 'name', $prefix )->delete();
		}
	}
}
