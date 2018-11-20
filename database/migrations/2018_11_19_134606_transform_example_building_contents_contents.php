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

			foreach(['insulated-glazing', 'floor-insulation'] as $section) {

				if ( array_key_exists( $section, $content ) ) {
					if ( array_key_exists( 'building_elements',
						$content[ $section ] ) ) {

						$shouldBe = [];

						// Floor insulation: has_crawlspace data fix
						if ( array_key_exists( 'crawlspace',
							$content[ $section ]['building_elements'] ) ) {
							$crawlspaceElement = \App\Models\Element::where( 'short',
								'crawlspace' )->first();
							if ( $crawlspaceElement instanceof \App\Models\Element ) {
								$extra = array_get( $content,
									$section . '.building_elements.' . $crawlspaceElement->id . '.extra',
									[] );
								if ( ! is_array( $extra ) ) {
									// only accessibility in there now
									$extra = [ 'access' => $extra ];
								}
								$extra['has_crawlspace'] = array_get( $content,
									$section . '.building_elements.crawlspace',
									'unknown' );
								$content[ $section ]['building_elements'][ $crawlspaceElement->id ]['extra'] = $extra;
							}
							unset( $content[ $section ]['building_elements']['crawlspace'] );
						}

						foreach ( $content[ $section ]['building_elements'] as $elementId => $elementValues ) {
							if (is_array($elementValues) && array_key_exists('extra', $elementValues)){
								$shouldBe[$elementId] = $elementValues;
							}
							else {
								foreach ( $elementValues as $short => $idOrArray ) {
									if ( $short == 'extra' ) {
										// take as-is
										$shouldBe[ $elementId ][ $short ] = $idOrArray;
									} else {
										// int (id)
										$shouldBe[ $elementId ] = (int) $idOrArray;
									}
								}
							}
						}

						if ( ! array_key_exists( 'element',
							$content[ $section ] ) ) {
							$content[ $section ]['element'] = [];
						}

						$content[ $section ]['element'] = array_replace_recursive( $content[ $section ]['element'],
							$shouldBe );
						unset( $content[ $section ]['building_elements'] );
					}
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
		/*
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
		*/
	}
}
