<?php

namespace Tests\Unit\app\Console\Commands;

use App\Console\Commands\Upgrade\AddMasterInputSource;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingHeater;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\UserInterest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AddMasterInputSourceTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

    public function testAddMasterInputSource()
    {
        // We need a building in the database for the command to function
        $building = factory(Building::class)->create();

        // We need the three input sources. They could be added through migrations so we check the database first
        $residentInputSource = DB::table('input_sources')
            ->where('short', InputSource::RESIDENT_SHORT)
            ->first();
        if(is_null($residentInputSource)) {
            $residentInputSource = factory(InputSource::class)->create([
                'name' => 'Bewoner',
                'short' => InputSource::RESIDENT_SHORT,
            ]);
        }
        $coachInputSource = DB::table('input_sources')
            ->where('short', InputSource::COACH_SHORT)
            ->first();
        if(is_null($coachInputSource)) {
            $coachInputSource = factory(InputSource::class)->create([
                'name' => 'Coach',
                'short' => InputSource::COACH_SHORT,
            ]);
        }
        $masterInputSource = DB::table('input_sources')
            ->where('short', InputSource::MASTER_SHORT)
            ->first();
        if(is_null($masterInputSource)) {
            $masterInputSource = factory(InputSource::class)->create([
                'name' => 'Master',
                'short' => InputSource::MASTER_SHORT,
            ]);
        }

        // We need to make relations so we can assert that data has indeed been added to the database
        // Create element values which will also make elements
        // We do this so the relation between element_id and element_value_id is correct
        $elementValues = factory(ElementValue::class, 2)->create();

        $relevantElementValue = $elementValues->first();
        $relevantBuildingElement = factory(BuildingElement::class)->create([
            'building_id' => $building->id,
            'input_source_id' => $coachInputSource->id,
            'element_id' => $relevantElementValue->element->id,
            'element_value_id' => $relevantElementValue->id,
        ]);

        $lessRelevantElementValue = $elementValues->last();

        $lessRelevantBuildingElement = factory(BuildingElement::class)->create([
            'building_id' => $building->id,
            'input_source_id' => $residentInputSource->id,
            'element_id' => $lessRelevantElementValue->element->id,
            'element_value_id' => $lessRelevantElementValue->id,
        ]);

        // Create user interests
        $relevantUserInterest = factory(UserInterest::class)->create([
            'user_id' => $building->user->id,
            'input_source_id' => $coachInputSource->id,
        ]);

        $lessRelevantUserInterest = factory(UserInterest::class)->create([
            'user_id' => $building->user->id,
            'input_source_id' => $residentInputSource->id,
        ]);

        // Create building heaters
        $relevantBuildingHeater = factory(BuildingHeater::class)->create([
            'building_id' => $building->id,
            'input_source_id' => $coachInputSource->id,
        ]);

        $lessRelevantBuildingHeater = factory(BuildingHeater::class)->create([
            'building_id' => $building->id,
            'input_source_id' => $residentInputSource->id,
        ]);

        // We have now created values that are related to the building. This means our command should be able to create
        // data for the master sources, and we can assert the 3 different ways the command processes data

        $this->artisan(AddMasterInputSource::class)->assertExitCode(0);

        // Now the command is done, we should test and see if our relevant relations have been properly migrated to
        // the master input source
        $newBuildingElement = DB::table('building_elements')
            ->where('building_id', $building->id)
            ->where('input_source_id', $masterInputSource->id)
            ->first();
        $this->assertNotEquals(null, $newBuildingElement);
        $this->assertEquals($relevantBuildingElement->element_id, optional($newBuildingElement)->element_id);
        $this->assertEquals($relevantBuildingElement->element_value_id, optional($newBuildingElement)->element_value_id);

        $newUserInterest = DB::table('user_interests')
            ->where('user_id', $building->user->id)
            ->where('input_source_id', $masterInputSource->id)
            ->first();
        $this->assertNotEquals(null, $newUserInterest);
        $this->assertEquals($relevantUserInterest->interested_in_type, optional($newUserInterest)->interested_in_type);
        $this->assertEquals($relevantUserInterest->interested_in_id, optional($newUserInterest)->interested_in_id);
        $this->assertEquals($relevantUserInterest->interest_id, optional($newUserInterest)->interest_id);

        $newBuildingHeater = DB::table('building_heaters')
            ->where('building_id', $building->id)
            ->where('input_source_id', $masterInputSource->id)
            ->first();
        $this->assertNotEquals(null, $newBuildingHeater);
        $this->assertEquals($relevantBuildingHeater->pv_panel_orientation_id, optional($newBuildingHeater)->pv_panel_orientation_id);
        $this->assertEquals($relevantBuildingHeater->angle, optional($newBuildingHeater)->angle);
    }

    public function testSearchCollectionForValue()
    {
        // Instantiate new command so we can call the function
        $command = new AddMasterInputSource();

        // Make a new input source
        $inputSource = factory(InputSource::class)->create();

        // Define our test object
        $test1 = (object) [
            'input_source_id' => $inputSource->id,
            'value' => $this->faker->word,
        ];

        // Add extra data
        $collection = collect([
            $test1,
            (object) [
                'input_source_id' => $inputSource->id + 1,
                'value' => $this->faker->word,
            ],
            (object) [
                'input_source_id' => $inputSource->id + 1,
                'value' => $this->faker->word,
            ],
        ]);

        // Call search
        $search = $command->searchCollectionForValue($collection, $inputSource);

        // It has to be an object, else we cannot check the object property
        if ($search instanceof \stdClass) {
            // Ensure the value is the same
            $this->assertEquals($search->value, $test1->value);

            // Repeat same test, except with a search this time
            $test2 = (object) [
                'input_source_id' => $inputSource->id,
                'extra_to_search' => $this->faker->uuid,
                'value' => $this->faker->word,
            ];

            $collection = collect([
                $test2,
                (object) [
                    'input_source_id' => $inputSource->id,
                    'extra_to_search' => $this->faker->uuid,
                    'value' => $this->faker->word,
                ],
                (object) [
                    'input_source_id' => $inputSource->id,
                    'extra_to_search' => $this->faker->uuid,
                    'value' => $this->faker->word,
                ],
            ]);

            $search = $command->searchCollectionForValue($collection, $inputSource, ['extra_to_search' => $test2->extra_to_search]);

            if ($search instanceof \stdClass) {
                $this->assertEquals($search->value, $test2->value);
            } else {
                $this->fail('Result for test2 is not an object');
            }
        } else {
            // If it's not an object, we will manually fail
            $this->fail('Result for test1 is not an object');
        }
    }

    public static function getObjectPropertyProvider()
    {
        return [
            [
                (object) [
                    'id' => 10,
                    'name' => 'Testcase',
                    'value' => true,
                ],
                'key',
                null,
            ],
            [
                (object) [
                    'id' => 10,
                    'name' => 'Testcase',
                    'value' => true,
                ],
                'name',
                'Testcase',
            ],
            [
                [
                    'id' => 10,
                    'name' => 'Testcase',
                    'value' => true,
                ],
                'name',
                null,
            ],
        ];
    }

    /**
     * @dataProvider getObjectPropertyProvider
     */
    public function testGetObjectProperty($object, $key, $expected)
    {
        // Instantiate new command so we can call the function
        $command = new AddMasterInputSource();

        $this->assertEquals($expected, $command->getObjectProperty($object, $key));
    }
}