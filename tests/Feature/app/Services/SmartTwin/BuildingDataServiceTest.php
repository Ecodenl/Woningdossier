<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Exceptions\AddressNotSupportedBySmartTwin;
use App\Models\Building;
use App\Models\BuildingType;
use App\Models\BuildingTypeCategory;
use App\Models\EnergyLabel;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\SmartTwin\Api\Resources\BuildingData;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\BuildingDataService;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * SmartTwin's Building Data: the building type, build year, surface and registered energy label of
 * an address, written over what was there (decision 2026-10-15-smarttwin-woninggegevens-building-data).
 */
final class BuildingDataServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;
    private InputSource $coach;
    private InputSource $master;
    private BuildingData&MockInterface $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
        $this->building->update(['postal_code' => '5224 HD', 'number' => '1', 'extension' => '']);
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
        $this->master = InputSource::findByShort(InputSource::MASTER_SHORT);

        $this->resource = Mockery::mock(BuildingData::class);
        $api = Mockery::mock(SmartTwinApi::class);
        $api->shouldReceive('buildingData')->andReturn($this->resource);
        $this->app->instance(SmartTwinApi::class, $api);
    }

    private function service(): BuildingDataService
    {
        return $this->app->make(BuildingDataService::class);
    }

    /**
     * @return array<string, mixed>
     */
    private function data(array $data = []): array
    {
        return array_merge([
            'isSupported'           => true,
            'buildingType'          => 3,
            'address'               => 'Teststraat 1, 5224 HD',
            'constructionYear'      => 1975,
            'livingSpaceArea'       => 112,
            'registeredEnergyLabel' => 'A+++',
        ], $data);
    }

    private function answer(string $short, InputSource $inputSource): mixed
    {
        return $this->building->getAnswer($inputSource, ToolQuestion::findByShort($short));
    }

    private function answerAs(string $short, mixed $value): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort($short))
            ->building($this->building)
            ->currentInputSource(InputSource::findByShort(InputSource::RESIDENT_SHORT))
            ->save($value);
    }

    private function category(string $short): int
    {
        return BuildingTypeCategory::where('short', $short)->value('id');
    }

    private function type(string $name): int
    {
        return BuildingType::all()->first(fn (BuildingType $type) => $type->getTranslation('name', 'nl') === $name)->id;
    }

    private function notFound(): ClientException
    {
        return new ClientException('Not Found', new Request('GET', 'basic-data-by-address'), new Response(404));
    }

    public function test_the_data_is_written_for_the_input_source_and_master(): void
    {
        $this->service()->apply($this->building, $this->coach, $this->data());

        foreach ([$this->coach, $this->master] as $inputSource) {
            $this->assertEquals($this->category('corner-house'), $this->answer('building-type-category', $inputSource));
            // A category with a single type gets that type too.
            $this->assertEquals($this->type('Hoekwoning'), $this->answer('building-type', $inputSource));
            $this->assertEquals(1975, $this->answer('build-year', $inputSource));
            $this->assertEquals(112, $this->answer('surface', $inputSource));
            $this->assertEquals(EnergyLabel::fromName('A+++')->id, $this->answer('energy-label', $inputSource));
        }
    }

    public function test_smart_twin_wins_over_what_was_there(): void
    {
        $this->answerAs('build-year', 1930);
        $this->answerAs('surface', 80);

        $this->service()->apply($this->building, $this->coach, $this->data());

        $this->assertEquals(1975, $this->answer('build-year', $this->master));
        $this->assertEquals(112, $this->answer('surface', $this->master));
    }

    public function test_what_smart_twin_does_not_know_or_the_question_would_not_take_is_left_alone(): void
    {
        $this->answerAs('build-year', 1930);
        $this->answerAs('surface', 80);
        $this->answerAs('energy-label', EnergyLabel::fromName('C')->id);
        $this->answerAs('building-type-category', $this->category('terraced-house'));

        $this->service()->apply($this->building, $this->coach, $this->data([
            'buildingType' => 0, 'constructionYear' => 0, 'livingSpaceArea' => 10, 'registeredEnergyLabel' => null,
        ]));

        $this->assertEquals($this->category('terraced-house'), $this->answer('building-type-category', $this->master));
        $this->assertEquals(1930, $this->answer('build-year', $this->master));
        $this->assertEquals(80, $this->answer('surface', $this->master));
        $this->assertEquals(EnergyLabel::fromName('C')->id, $this->answer('energy-label', $this->master));
    }

    public function test_what_smart_twin_does_not_write_stays_in_master(): void
    {
        // The coach has no row yet; without DwellingRows the coach's new row would empty master.
        $this->answerAs('monument', 1);
        $this->answerAs('building-layers', 2);

        $this->service()->apply($this->building, $this->coach, $this->data());

        $this->assertEquals(1, $this->answer('monument', $this->master));
        $this->assertEquals(2, $this->answer('building-layers', $this->master));
    }

    public function test_an_apartment_clears_a_type_that_is_no_apartment(): void
    {
        $this->answerAs('building-type-category', $this->category('corner-house'));
        $this->assertEquals($this->type('Hoekwoning'), $this->answer('building-type', $this->master));

        $this->service()->apply($this->building, $this->coach, $this->data(['buildingType' => 5]));

        $this->assertEquals($this->category('apartment'), $this->answer('building-type-category', $this->master));
        $this->assertEmpty($this->answer('building-type', $this->master));
    }

    public function test_an_apartment_keeps_the_kind_of_apartment_someone_chose(): void
    {
        $this->answerAs('building-type-category', $this->category('apartment'));
        $this->answerAs('building-type', $this->type('Bovenwoning hoek'));

        $this->service()->apply($this->building, $this->coach, $this->data(['buildingType' => 5]));

        $this->assertEquals($this->type('Bovenwoning hoek'), $this->answer('building-type', $this->master));
    }

    public function test_an_unsupported_address_is_reported_with_the_address_and_the_cooperation(): void
    {
        Exceptions::fake();
        $this->resource->shouldReceive('basicDataByAddress')->andReturn($this->data(['isSupported' => false]));

        $this->service()->refresh($this->building, $this->coach);

        $cooperation = $this->building->user->cooperation;
        Exceptions::assertReported(fn (AddressNotSupportedBySmartTwin $e) => str_contains($e->getMessage(), '5224 HD 1')
            && str_contains($e->getMessage(), $cooperation->name)
            && $e->context()['cooperation_id'] === $cooperation->id
        );
        // What it does know is still written.
        $this->assertEquals(1975, $this->answer('build-year', $this->master));
    }

    public function test_a_supported_address_is_not_reported(): void
    {
        Exceptions::fake();
        $this->resource->shouldReceive('basicDataByAddress')->andReturn($this->data());

        $this->service()->refresh($this->building, $this->coach);

        Exceptions::assertNotReported(AddressNotSupportedBySmartTwin::class);
    }

    public function test_the_address_is_sent_without_spaces_in_the_postal_code(): void
    {
        $this->resource->shouldReceive('basicDataByAddress')
            ->once()
            ->with(['postal_code' => '5224HD', 'house_number' => 1])
            ->andReturn($this->data());

        $this->assertSame(1975, $this->service()->fetch($this->building)['constructionYear']);
    }

    public function test_an_extension_is_tried_as_an_addition_then_as_a_house_letter(): void
    {
        $this->building->update(['extension' => 'b']);

        $this->resource->shouldReceive('basicDataByAddress')
            ->with(['postal_code' => '5224HD', 'house_number' => 1, 'house_number_addition' => 'B'])
            ->once()->andThrow($this->notFound());
        $this->resource->shouldReceive('basicDataByAddress')
            ->with(['postal_code' => '5224HD', 'house_number' => 1, 'house_letter' => 'B'])
            ->once()->andReturn($this->data());

        $this->assertSame(1975, $this->service()->fetch($this->building)['constructionYear']);
    }

    public function test_the_ways_an_extension_can_be_read(): void
    {
        $address = ['postal_code' => '5224HD', 'house_number' => 1];

        $this->building->extension = 'C-1';
        $this->assertSame([
            $address + ['house_number_addition' => 'C1'],
            $address + ['house_letter' => 'C', 'house_number_addition' => '1'],
        ], $this->service()->addressesFor($this->building));

        $this->building->extension = '12';
        $this->assertSame([$address + ['house_number_addition' => '12']], $this->service()->addressesFor($this->building));

        // Too long to be an addition, and not a letter with one: no way to send it.
        $this->building->extension = '123456';
        $this->assertSame([], $this->service()->addressesFor($this->building));
    }

    public function test_an_address_smart_twin_does_not_know_writes_nothing(): void
    {
        $this->answerAs('build-year', 1930);
        $this->resource->shouldReceive('basicDataByAddress')->andThrow($this->notFound());

        $this->service()->refresh($this->building, $this->coach);

        $this->assertEquals(1930, $this->answer('build-year', $this->master));
    }

    public function test_any_other_error_is_left_to_the_job_to_retry(): void
    {
        $this->resource->shouldReceive('basicDataByAddress')
            ->andThrow(new ClientException('Unauthorized', new Request('GET', 'basic-data-by-address'), new Response(401)));

        $this->expectException(ClientException::class);

        $this->service()->refresh($this->building, $this->coach);
    }
}
