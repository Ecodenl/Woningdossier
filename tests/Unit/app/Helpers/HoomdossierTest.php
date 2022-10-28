<?php

namespace Tests\Unit\app\Helpers;

use Database\Seeders\StatusesTableSeeder;
use Database\Seeders\InputSourcesTableSeeder;
use App\Helpers\Hoomdossier;
use App\Models\Account;
use App\Models\Building;
use App\Models\BuildingVentilation;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @please-read-me
 * its almost impossible to test the getMostCredibleValueFrom collection, this does not return a input source
 * there are ways to check, but that would be pure guess work. Besides that, if credible value passes, so does the collection.
 */
class HoomdossierTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(StatusesTableSeeder::class);
        $this->seed(InputSourcesTableSeeder::class);
    }

    public function testGetMostCredibleValue()
    {
        $cooperation = Cooperation::factory()->create();

        $account = Account::factory()->create();

        $residentUser = User::factory()->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => $account->id,
        ]);
        $building = Building::factory()->create(['user_id' => $residentUser->id]);

        // just create some building ventilations for each input source
        foreach (InputSource::all() as $inputSource) {
            BuildingVentilation::factory()->create([
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
        }

        $credibleValue = Hoomdossier::getMostCredibleValue($building->buildingVentilations());

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $this->assertEquals($masterInputSource->id, $credibleValue->input_source_id);
    }

}
