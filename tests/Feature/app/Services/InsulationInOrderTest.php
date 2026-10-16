<?php

namespace Tests\Feature\app\Services;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\SmartTwin\Mapping\Mappers\ElementValues;
use App\Services\ToolQuestionService;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * "In orde" from Goede isolatie is the boundary for the cards a SmartTwin import makes. The
 * calculated woonplan keeps Matige isolatie, also in a cooperation that takes part in SmartTwin:
 * calculated advices only exist there until SmartTwin's first advice, and until then the woonplan
 * stays as it was.
 */
final class InsulationInOrderTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $user = User::factory()->withAccount()->asResident()->create(['cooperation_id' => $this->cooperation->id]);
        $this->building = Building::factory()->create(['user_id' => $user->id]);
    }

    private function wallInsulation(int $calculateValue): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort('current-wall-insulation'))
            ->building($this->building)
            ->currentInputSource(InputSource::findByShort(InputSource::RESIDENT_SHORT))
            ->save(app(ElementValues::class)->idFor('wall-insulation', $calculateValue));
    }

    private function category(): string
    {
        return UserActionPlanAdviceService::getCategoryFromMeasure(
            $this->building->fresh(),
            MeasureApplication::findByShort('cavity-wall-insulation'),
        );
    }

    public function test_with_smart_twin_the_calculated_woonplan_keeps_its_boundary(): void
    {
        $this->wallInsulation(3); // Matige isolatie

        $this->assertSame(UserActionPlanAdviceService::CATEGORY_COMPLETE, $this->category());
    }

    public function test_good_starts_at_goede_isolatie(): void
    {
        $elementValues = app(ElementValues::class);

        $this->assertFalse(ElementValue::find($elementValues->idFor('wall-insulation', 4))->countsAsGood()); // Redelijke isolatie
        $this->assertTrue(ElementValue::find($elementValues->idFor('wall-insulation', 5))->countsAsGood()); // Goede isolatie
        $this->assertFalse(ElementValue::find($elementValues->idFor('floor-insulation', 5))->countsAsGood()); // Redelijke isolatie
        $this->assertTrue(ElementValue::find($elementValues->idFor('roof-insulation', 6))->countsAsGood()); // Goede isolatie
    }
}
