<?php

namespace App\Enums;

use App\Models\ElementValue;

/**
 * The glass types of living-rooms-windows and sleeping-rooms-windows, by the order of their element
 * value. Those values carry no calculate value, so their order is what tells them apart.
 *
 * Enkelglas through Drievoudige beglazing are the options the scans ask. HR, HR+ and vacuum glass
 * were added for SmartTwin, which tells them apart (decision 2026-11-07-smarttwin-glasmapping). The
 * scans do not ask them, and they come after the original four so that a database already seeded
 * keeps its rows: ElementsValuesTableSeeder matches on order.
 */
enum GlassType: int
{
    case SINGLE = 0;
    case DOUBLE = 1;
    case HR_PLUS_PLUS = 2;
    case TRIPLE = 3;
    case HR = 4;
    case HR_PLUS = 5;
    case VACUUM = 6;

    /**
     * The glass that counts as in order, both for the category of the old woonplan
     * (UserActionPlanAdviceService) and for SmartTwin's "In orde" cards (InOrderCards). Raising or
     * lowering the bar is this one line. Stakeholder decision of 7 November 2026: HR and better, at
     * the customer's request (first built from HR+).
     */
    private const IN_ORDER_FROM = self::HR;

    /** Whether the scans offer it as an option; the others only come from SmartTwin. */
    public const ASKED_IN_SCANS = [self::SINGLE, self::DOUBLE, self::HR_PLUS_PLUS, self::TRIPLE];

    public static function of(ElementValue $elementValue): ?self
    {
        return self::tryFrom((int) $elementValue->order);
    }

    /** Worst to best: what "in order from" and "the worst wins" compare. */
    public function rank(): int
    {
        return match ($this) {
            self::SINGLE       => 0,
            self::DOUBLE       => 1,
            self::HR           => 2,
            self::HR_PLUS      => 3,
            self::HR_PLUS_PLUS => 4,
            self::TRIPLE       => 5,
            self::VACUUM       => 6,
        };
    }

    public function isInOrder(): bool
    {
        return $this->rank() >= self::IN_ORDER_FROM->rank();
    }

    public function isAskedInScans(): bool
    {
        return in_array($this, self::ASKED_IN_SCANS, true);
    }
}
