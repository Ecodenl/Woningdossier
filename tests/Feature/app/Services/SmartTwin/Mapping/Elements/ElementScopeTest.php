<?php

namespace Tests\Feature\app\Services\SmartTwin\Mapping\Elements;

use App\Models\MeasureApplication;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ElementScope keys on step shorts, which only the seeded steps can confirm: a slug in their place
 * (verwarming for heating) reads fine and leaves every measure of that step without an element.
 */
final class ElementScopeTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    public function test_every_measure_outside_the_small_measures_works_on_an_element(): void
    {
        $measures = app(SolutionMeasures::class);

        $withoutElement = MeasureApplication::all()
            ->reject(fn (MeasureApplication $measure) => in_array($measures->stepFor($measure->short), ['small-measures', 'small-measures-lite'], true))
            ->filter(fn (MeasureApplication $measure) => [] === ElementScope::forMeasure($measure->short, $measures->stepFor($measure->short)))
            ->pluck('short')
            ->values()
            ->all();

        $this->assertSame([], $withoutElement);
    }
}
