<?php

namespace Tests\Unit\app\Console\Commands;

use App\Console\Commands\Upgrade\AddMasterInputSource;
use App\Models\InputSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddMasterInputSourceTest extends TestCase
{
    use RefreshDatabase,
        WithFaker;

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
}