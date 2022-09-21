<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Hoomdossier;
use App\Helpers\ToolQuestionHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingVentilation;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @please-read-me
 * its almost impossible to test the getMostCredibleValueFrom collection, this does not return a input source
 * there are ways to check, but that would be pure guess work. Besides that, if credible value passes, so does the collection.
 */
class ToolQuestionHelperTest extends TestCase
{
    use DatabaseTransactions;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\StatusesTableSeeder::class);
        $this->seed(\InputSourcesTableSeeder::class);
    }

    public function testResolveSaveIn()
    {
        $cooperation = factory(Cooperation::class)->create();

        $account = factory(Account::class)->create();

        $residentUser = factory(User::class)->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => $account->id,
        ]);
        $building = factory(Building::class)->create(['user_id' => $residentUser->id]);

        $provider = [
            [
                'building_features.roof_type_id',
                [
                    'table' => 'building_features',
                    'column' => 'roof_type_id',
                    'where' => [
                        'building_id' => $building->id,
                    ],
                ]
            ],
            [
                'building_features.roof_type_id',
                [
                    'table' => 'building_features',
                    'column' => 'roof_type_id',
                    'where' => [
                        'building_id' => $building->id,
                    ],
                ]
            ],
            [
                'considerables.App\\Models\\Step.3.is_considering',
                [
                    'table' => 'considerables',
                    'column' => 'is_considering',
                    'where' => [
                        'considerable_type' => 'App\Models\Step',
                        'considerable_id' => 3,
                        'user_id' => $residentUser->id,
                    ],
                ]
            ]
        ];

        foreach($provider as $assertable) {
            $this->assertEquals($assertable[1], ToolQuestionHelper::resolveSaveIn($assertable[0], $building));
        }
    }

}
