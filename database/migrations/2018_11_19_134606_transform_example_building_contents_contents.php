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

		$buildingFeaturesPerSection = [
			'general-data' => ['surface', ],
			'wall-insulation' => ['wall_surface', 'cavity_wall', 'facade_plastered_painted', 'facade_damaged_paintwork_id', 'wall_joints', 'contaminated_wall_joints', ],
			'insulated-glazing' => ['window_surface', ],
			'floor-insulation' => ['floor_surface', ],
			'roof-insulation' => ['roof_type_id', ],
		];

		foreach ( $exampleBuildingContents as $exampleBuildingContent ) {

			$content = $exampleBuildingContent->content;

			foreach(array_keys($content) as $section) {

				// Move particular columns to building_features
				if (array_key_exists($section, $buildingFeaturesPerSection)){
					foreach($buildingFeaturesPerSection[$section] as $featureColumn){
						if (array_key_exists($featureColumn, $content[$section])){
							if (!array_key_exists('building_features', $content[$section])){
								$content[$section]['building_features'] = [];
							}
							$content[$section]['building_features'][$featureColumn] = $content[$section][$featureColumn];
							unset($content[$section][$featureColumn]);
						}
					}
				}

				if ( array_key_exists( $section, $content ) ) {

					// move particular building_elements
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

					// move roof types from category (which will become 'short' later on) to roof type id
					if(array_key_exists('building_roof_types', $content[$section])){
						$move = ['pitched' => 1, 'flat' => 2, ];

						foreach($move as $cat => $rtid) {
							if ( array_key_exists( $cat,
								$content[ $section ]['building_roof_types'] ) ) {
								$content[ $section ]['building_roof_types'][$rtid] = $content[ $section ]['building_roof_types'][$cat];

								unset( $content[ $section ]['building_roof_types'][$cat] );
							}
							if (array_key_exists('surface', $content[ $section ]['building_roof_types'][$rtid])){
								$content[ $section ]['building_roof_types'][$rtid]['roof_surface'] = $content[ $section ]['building_roof_types'][$rtid]['surface'];
								unset($content[ $section ]['building_roof_types'][$rtid]['surface']);
							}
						}
					}

					if(array_key_exists('building_services', $content[$section])){
						$content[$section]['service'] = $content[$section]['building_services'];
						unset($content[$section]['building_services']);
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
