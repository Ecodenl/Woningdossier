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
        $command = new AddMasterInputSource();

        $inputSource = factory(InputSource::class)->create();

        $test1 = (object) [
            'input_source_id' => $inputSource->id,
            'value' => $this->faker->word,
        ];

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

        $search = $command->searchCollectionForValue($collection, $inputSource);

        if ($search instanceof \stdClass) {
            $this->assertEquals($search->value, $test1->value);

            $test2 = (object) [
                'input_source_id' => $inputSource->id,
                'extra_to_search' => $this->faker->uuid,
                'value' => $this->faker->word,
            ];

            $collection = collect([
                $test2 = (object) [
                    'input_source_id' => $inputSource->id,
                    'extra_to_search' => $this->faker->uuid,
                    'value' => $this->faker->word,
                ],
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
            $this->fail('Result for test1 is not an object');
        }
    }
}