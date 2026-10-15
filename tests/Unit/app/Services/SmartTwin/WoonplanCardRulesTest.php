<?php

namespace Tests\Unit\app\Services\SmartTwin;

use App\Enums\AdviceSource;
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

    private function advice(?AdviceSource $source, string $category, ?string $executedAt = null): UserActionPlanAdvice
    {
        $advice = new UserActionPlanAdvice();
        $advice->source = $source;
        $advice->category = $category;
        $advice->executed_at = $executedAt;

        return $advice;
    }

    public function test_every_card_tells_what_it_is(): void
    {
        $this->assertSame(Rules::PACKAGE, Rules::kindOf($this->advice(AdviceSource::SMART_TWIN, self::NOW)));
        $this->assertSame(Rules::POSSIBLE, Rules::kindOf($this->advice(AdviceSource::SMART_TWIN, self::LATER)));
        $this->assertSame(Rules::IN_ORDER, Rules::kindOf($this->advice(AdviceSource::SMART_TWIN, self::IN_ORDER)));
        $this->assertSame(Rules::EXECUTED, Rules::kindOf($this->advice(AdviceSource::SMART_TWIN, self::IN_ORDER, '2026-10-01')));
        // Own and cooperation measures, and the small measures, have no source.
        $this->assertSame(Rules::OWN, Rules::kindOf($this->advice(null, self::LATER)));
    }

    public function test_every_card_may_be_reordered_within_its_column(): void
    {
        foreach ([Rules::PACKAGE, Rules::POSSIBLE, Rules::IN_ORDER, Rules::EXECUTED, Rules::OWN] as $kind) {
            $this->assertSame(Rules::ALLOWED, Rules::move($kind, self::NOW, self::NOW), $kind);
        }
    }

    public function test_a_package_card_stays_in_its_column(): void
    {
        $this->assertSame(Rules::DENIED, Rules::move(Rules::PACKAGE, self::NOW, self::LATER));
        $this->assertFalse(Rules::mayTrash(Rules::PACKAGE));
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
        $this->assertSame([self::IN_ORDER, self::NOW], Rules::targets(Rules::PACKAGE, self::NOW));
        $this->assertSame([self::IN_ORDER, self::NOW, self::LATER], Rules::targets(Rules::OWN, self::NOW));
        $this->assertSame([self::IN_ORDER], Rules::targets(Rules::EXECUTED, self::IN_ORDER));
    }
}
