<?php

namespace Tests\Unit\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\WoonplanCardRules as Rules;
use Tests\TestCase;

/**
 * The card rules of a SmartTwin woonplan, after the woonplan design "Pagina 2" and the stuurgroep.
 *
 * On the Laravel TestCase rather than PHPUnit's: the cards are Eloquent models.
 */
final class WoonplanCardRulesTest extends TestCase
{
    private const NOW = 'to-do';
    private const LATER = 'later';
    private const IN_ORDER = 'complete';

    private const PACKAGE_MEASURE = 12;
    private const OTHER_MEASURE = 30;

    private function advice(?AdviceSource $source, string $category, ?string $executedAt = null, int $measureId = self::PACKAGE_MEASURE): UserActionPlanAdvice
    {
        $advice = new UserActionPlanAdvice();
        $advice->source = $source;
        $advice->category = $category;
        $advice->executed_at = $executedAt;
        $advice->user_action_plan_advisable_type = MeasureApplication::class;
        $advice->user_action_plan_advisable_id = $measureId;

        return $advice;
    }

    private function kindOf(UserActionPlanAdvice $advice): string
    {
        return Rules::kindOf($advice, [self::PACKAGE_MEASURE, 7]);
    }

    public function test_every_card_tells_what_it_is(): void
    {
        $this->assertSame(Rules::PACKAGE, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::NOW)));
        $this->assertSame(Rules::POSSIBLE, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::LATER, measureId: self::OTHER_MEASURE)));
        $this->assertSame(Rules::IN_ORDER, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::IN_ORDER, measureId: self::OTHER_MEASURE)));
        $this->assertSame(Rules::EXECUTED, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::IN_ORDER, '2026-10-01')));
        // Own and cooperation measures, and the small measures, have no source.
        $this->assertSame(Rules::OWN, $this->kindOf($this->advice(null, self::LATER)));
    }

    public function test_a_package_card_stays_a_package_card_in_later(): void
    {
        // The package decides, not the column: moved to "Later uitvoeren" it may still come back.
        $this->assertSame(Rules::PACKAGE, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::LATER)));
        // A SmartTwin card the package does not hold is a possible measure, in any column.
        $this->assertSame(Rules::POSSIBLE, $this->kindOf($this->advice(AdviceSource::SMART_TWIN, self::NOW, measureId: self::OTHER_MEASURE)));
    }

    public function test_every_card_may_be_reordered_within_its_column(): void
    {
        foreach ([Rules::PACKAGE, Rules::POSSIBLE, Rules::IN_ORDER, Rules::EXECUTED, Rules::OWN] as $kind) {
            $this->assertSame(Rules::ALLOWED, Rules::move($kind, self::NOW, self::NOW), $kind);
        }
    }

    public function test_a_package_card_moves_between_now_and_later_and_may_go(): void
    {
        $this->assertSame(Rules::ALLOWED, Rules::move(Rules::PACKAGE, self::NOW, self::LATER));
        $this->assertSame(Rules::ALLOWED, Rules::move(Rules::PACKAGE, self::LATER, self::NOW));
        $this->assertTrue(Rules::mayTrash(Rules::PACKAGE));
    }

    public function test_a_possible_measure_stays_in_its_column_but_may_go(): void
    {
        $this->assertSame(Rules::DENIED, Rules::move(Rules::POSSIBLE, self::LATER, self::NOW));
        $this->assertTrue(Rules::mayTrash(Rules::POSSIBLE));
    }

    public function test_an_own_measure_moves_freely_and_may_go(): void
    {
        $this->assertSame(Rules::ALLOWED, Rules::move(Rules::OWN, self::NOW, self::LATER));
        $this->assertSame(Rules::ALLOWED, Rules::move(Rules::OWN, self::LATER, self::NOW));
        $this->assertTrue(Rules::mayTrash(Rules::OWN));
    }

    public function test_into_in_order_means_carried_out(): void
    {
        foreach ([Rules::PACKAGE, Rules::POSSIBLE, Rules::OWN] as $kind) {
            $this->assertSame(Rules::EXECUTE, Rules::move($kind, self::NOW, self::IN_ORDER), $kind);
        }
    }

    public function test_what_is_in_order_stays_there(): void
    {
        foreach ([Rules::IN_ORDER, Rules::EXECUTED] as $kind) {
            $this->assertSame(Rules::DENIED, Rules::move($kind, self::IN_ORDER, self::NOW), $kind);
            $this->assertFalse(Rules::mayTrash($kind), $kind);
        }
    }

    public function test_the_browser_gets_the_same_targets(): void
    {
        $this->assertSame([self::IN_ORDER, self::NOW, self::LATER], Rules::targets(Rules::PACKAGE, self::NOW));
        $this->assertSame([self::IN_ORDER, self::NOW, self::LATER], Rules::targets(Rules::OWN, self::NOW));
        $this->assertSame([self::IN_ORDER], Rules::targets(Rules::EXECUTED, self::IN_ORDER));
    }

    /**
     * MyPlan\Form::cardMoved() carries a card's targets along to its new column rather than
     * reloading it. That only holds while a card that may cross between "Nu aanpakken" and "Later
     * uitvoeren" has the same targets in both; a rule that tells them apart needs the reload.
     */
    public function test_a_card_that_crosses_columns_has_the_same_targets_in_both(): void
    {
        foreach ([Rules::PACKAGE, Rules::POSSIBLE, Rules::IN_ORDER, Rules::EXECUTED, Rules::OWN] as $kind) {
            if (Rules::DENIED === Rules::move($kind, self::NOW, self::LATER)) {
                continue;
            }

            $this->assertSame(Rules::targets($kind, self::NOW), Rules::targets($kind, self::LATER), $kind);
        }
    }
}
