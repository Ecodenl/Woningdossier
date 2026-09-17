<?php

namespace Tests\Feature\app\Services\SmartTwin\Mapping\Mappers;

use App\Models\Element;
use App\Services\SmartTwin\Mapping\Mappers\ElementValues;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The one step in the mapping that goes to the database: element value ids differ per environment,
 * the calculate values the mapping reasons in do not.
 */
final class ElementValuesTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private ElementValues $elementValues;

    protected function setUp(): void
    {
        parent::setUp();

        $this->elementValues = app(ElementValues::class);
    }

    public function test_it_finds_the_option_belonging_to_a_calculate_value(): void
    {
        $expected = Element::findByShort('wall-insulation')
            ->values()
            ->where('calculate_value', 2) // Geen isolatie
            ->value('id');

        $this->assertNotNull($expected);
        $this->assertSame($expected, $this->elementValues->idFor('wall-insulation', 2));
    }

    public function test_the_whole_facade_scale_resolves(): void
    {
        // The mapping can produce any of these, so every one of them has to exist. Also catches the
        // seeder and InsulationQuality drifting apart on which calculate values the scale holds.
        foreach ([2, 3, 4, 5, 6] as $calculateValue) {
            $this->assertNotNull(
                $this->elementValues->idFor('wall-insulation', $calculateValue),
                "geen element value met calculate_value {$calculateValue}",
            );
        }
    }

    public function test_it_returns_nothing_for_a_level_that_does_not_exist(): void
    {
        $this->assertNull($this->elementValues->idFor('wall-insulation', 99));
    }

    public function test_it_returns_nothing_for_an_element_that_does_not_exist(): void
    {
        $this->assertNull($this->elementValues->idFor('bestaat-niet', 2));
    }
}
