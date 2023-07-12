<?php

namespace Tests\Unit\app\Services\Lvbag;

use App\Services\Lvbag\BagService;
use Database\Seeders\DatabaseSeeder;
use Ecodenl\LvbagPhpWrapper\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use Tests\TestCase;

class BagServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('cache:clear');
    }

    public function test_list_address_expanded_should_not_clear_bag_fields_on_endpoint_exception()
    {
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) {
                return $mock->shouldReceive('request')
                    ->andThrow(new \Exception('Err', 500));
            }
        );

        $attributes = [
            'postcode' => '3255mc',
            'huisnummer' => 10,
        ];

        $addressExpanded = app(BagService::class)->listAddressExpanded($attributes);

        $this->assertTrue($addressExpanded->expandedAddress['endpoint_failure']);
        $buildingData = $addressExpanded->prepareForBuilding();
        $this->assertArrayNotHasKey('bag_addressid', $buildingData);
        $this->assertArrayNotHasKey('bag_woonplaats_id', $buildingData);
    }

    public function test_list_address_expanded_should_clear_bag_fields_on_empty_response()
    {
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) {
                return $mock->shouldReceive('get')
                    ->andReturn([]);
            }
        );

        $attributes = [
            'postcode' => '3255mc',
            'huisnummer' => 10,
        ];

        $addressExpanded = app(BagService::class)->listAddressExpanded($attributes);

        $this->assertFalse($addressExpanded->expandedAddress['endpoint_failure']);
        $buildingData = $addressExpanded->prepareForBuilding();
        $this->assertEmpty($buildingData['bag_addressid']);
        $this->assertEmpty($buildingData['bag_woonplaats_id']);
    }

    public function test_list_address_expanded_has_bag_ids_on_filled_response()
    {
        $mockedApiData = [
            "_embedded" => [
                "adressen" => [
                    [
                        "nummeraanduidingIdentificatie" => "1924200000030235",
                        "woonplaatsIdentificatie" => "2134",
                        "openbareRuimteNaam" => "Boezemweg",
                        "huisnummer" => 11,
                        "postcode" => '1234AB',
                        "woonplaatsNaam" => "Oude-Tonge",
                        "oorspronkelijkBouwjaar" => [
                            0 => "2015"
                        ],
                        "oppervlakte" => 2666,
                    ],
                ],
            ]
        ];
        $this->partialMock(
            Client::class,
            function (MockInterface $mock) use ($mockedApiData) {
                return $mock
                    ->shouldReceive('get')
                    ->andReturn($mockedApiData);
            }
        );

        $attributes = [
            'postcode' => '3255mc',
            'huisnummer' => 10,
        ];

        $addressExpanded = app(BagService::class)->listAddressExpanded($attributes);

        $this->assertFalse($addressExpanded->expandedAddress['endpoint_failure']);
        $buildingData = $addressExpanded->prepareForBuilding();
        $this->assertSame($buildingData['bag_addressid'], $mockedApiData['_embedded']['adressen'][0]['nummeraanduidingIdentificatie']);
        $this->assertSame($buildingData['bag_woonplaats_id'], $mockedApiData['_embedded']['adressen'][0]['woonplaatsIdentificatie']);
    }
}
