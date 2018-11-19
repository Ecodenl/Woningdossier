<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransformExampleBuildingContentsContents extends Migration {

	/**
	 * Transform the example buildings content where needed
	 */

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$exampleBuildingContents = \App\Models\ExampleBuildingContent::all();

		foreach ( $exampleBuildingContents as $exampleBuildingContent ) {

			$content = $exampleBuildingContent->content;

			if ( array_key_exists( 'insulated-glazing', $content ) ) {
				if ( array_key_exists( 'building_elements',
					$content['insulated-glazing'] ) ) {
					$shouldBe = [];
					//dd($content['insulated-glazing']['building_elements']);
					foreach ( $content['insulated-glazing']['building_elements'] as $elementId => $elementValues ) {
						foreach ( $elementValues as $short => $elementValueId ) {
							$shouldBe[ $elementId ] = (int) $elementValueId;
						}
					}

					if ( ! array_key_exists( 'element',
						$content['insulated-glazing'] ) ) {
						$content['insulated-glazing']['element'] = [];
					}

					$content['insulated-glazing']['element'] = array_replace_recursive( $content['insulated-glazing']['element'],
						$shouldBe );
					unset( $content['insulated-glazing']['building_elements'] );
				}
			}

			$exampleBuildingContent->content = $content;
			$exampleBuildingContent->save();
		}


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$exampleBuildingContents = \App\Models\ExampleBuildingContent::all();

		foreach ( $exampleBuildingContents as $exampleBuildingContent ) {

			$content = $exampleBuildingContent->content;

			if ( array_key_exists( 'insulated-glazing', $content ) ) {
				if ( array_key_exists( 'element',
					$content['insulated-glazing'] ) ) {
					$revert = [];
					foreach ( $content['insulated-glazing']['element'] as $elementId => $elementValueId ) {
						$element = \App\Models\Element::find( $elementId );
						if ( $element instanceof \App\Models\Element ) {
							$revert[ $element->id ] = [ $element->short => $elementValueId ];
						}
					}

					if ( ! array_key_exists( 'building_elements',
						$content['insulated-glazing'] ) ) {
						$content['insulated-glazing']['building_elements'] = [];
					}

					$content['insulated-glazing']['building_elements'] = array_replace_recursive( $content['insulated-glazing']['building_elements'],
						$revert );
					unset( $content['insulated-glazing']['elements'] );
				}
			}

			$exampleBuildingContent->content = $content;
			$exampleBuildingContent->save();
		}
	}
}
