<?php

namespace Tests\Unit\app\Models;

use App\Enums\AdviceSource;
use App\Models\CustomMeasureApplication;
use App\Models\MeasureApplication;
use App\Models\SmartTwinPackage;
use App\Models\UserActionPlanAdvice;
use Tests\TestCase;

/**
 * On the Laravel TestCase rather than PHPUnit's: the cards are Eloquent models, and building one
 * needs the container — which a plain unit test only has if an earlier test happened to leave it.
 *
 * The figures are those of the first example response: 1.623,7 m³ gas now and 1.339,8 after the
 * package, label C to B.
 */
final class SmartTwinPackageTest extends TestCase
{
    private function package(): SmartTwinPackage
    {
        return new SmartTwinPackage([
            'measure_application_ids' => [12, 7, 9],
            'current_result'          => ['energyConsumptionGas' => 1623.6915, 'energyLabel' => 'C'],
            'scenario_result'         => ['energyConsumptionGas' => 1339.7759, 'energyLabel' => 'B'],
        ]);
    }

    public function test_the_gas_saving_is_now_minus_after(): void
    {
        $this->assertEqualsWithDelta(283.92, $this->package()->gasSavings(), 0.001);
        $this->assertSame('B', $this->package()->labelAfter());
    }

    public function test_the_package_is_intact_whatever_the_order_of_the_cards(): void
    {
        $this->assertTrue($this->package()->isIntactFor([9, 12, 7]));
    }

    public function test_a_missing_package_measure_breaks_the_package(): void
    {
        $this->assertFalse($this->package()->isIntactFor([12, 7]));
    }

    public function test_another_card_in_to_do_breaks_the_package(): void
    {
        $this->assertFalse($this->package()->isIntactFor([12, 7, 9, 30]));
    }

    public function test_without_figures_there_is_no_saving(): void
    {
        $this->assertNull((new SmartTwinPackage(['measure_application_ids' => []]))->gasSavings());
    }

    private function card(string $type, int $id, string $category = 'to-do', bool $visible = true, ?AdviceSource $source = AdviceSource::SMART_TWIN): UserActionPlanAdvice
    {
        $advice = new UserActionPlanAdvice();
        $advice->user_action_plan_advisable_type = $type;
        $advice->user_action_plan_advisable_id = $id;
        $advice->category = $category;
        $advice->visible = $visible;
        $advice->source = MeasureApplication::class === $type ? $source : null;

        return $advice;
    }

    public function test_the_plan_holding_the_package_in_to_do_is_intact(): void
    {
        $plan = [
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9),
        ];

        $this->assertTrue($this->package()->isIntactIn($plan));
    }

    public function test_a_package_card_moved_to_later_leaves_it_intact(): void
    {
        // The stuurgroep: moving a package card between the columns leaves the package intact.
        $this->assertTrue($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9, 'later'),
        ]));

        $this->assertTrue($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12, 'later'),
            $this->card(MeasureApplication::class, 7, 'later'),
            $this->card(MeasureApplication::class, 9, 'later'),
        ]));
    }

    public function test_a_smart_twin_card_outside_the_package_in_later_leaves_it_intact(): void
    {
        $this->assertTrue($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9),
            $this->card(MeasureApplication::class, 30, 'later'),
        ]));
    }

    public function test_a_package_card_trashed_or_carried_out_breaks_it(): void
    {
        $this->assertFalse($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9, 'later', visible: false),
        ]));

        $this->assertFalse($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9, 'complete'),
        ]));

        $this->assertFalse($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9, visible: false),
        ]));
    }

    public function test_an_own_measure_in_to_do_leaves_the_package_intact_for_now(): void
    {
        $this->assertFalse(SmartTwinPackage::OWN_MEASURES_BREAK_PACKAGE);

        $this->assertTrue($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9),
            $this->card(CustomMeasureApplication::class, 3),
        ]));
    }

    public function test_a_small_measure_in_to_do_counts_as_an_own_measure(): void
    {
        // A small measure is a calculated card that stays next to SmartTwin's advice.
        $this->assertTrue($this->package()->isIntactIn([
            $this->card(MeasureApplication::class, 12),
            $this->card(MeasureApplication::class, 7),
            $this->card(MeasureApplication::class, 9),
            $this->card(MeasureApplication::class, 30, source: null),
        ]));
    }
}
