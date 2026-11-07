<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Mappers\ElementValues;

/**
 * Hands back a recognisable id per calculate value, so a test can assert which level was chosen
 * without seeding element values.
 */
final class FakeElementValues extends ElementValues
{
    public const OFFSET = 900;
    public const ORDER_OFFSET = 800;

    /** Calculate values this pretends not to have. */
    public array $missing = [];

    public function idFor(string $elementShort, int $calculateValue): ?int
    {
        return in_array($calculateValue, $this->missing, true) ? null : self::OFFSET + $calculateValue;
    }

    public function idForOrder(string $elementShort, int $order): ?int
    {
        return self::ORDER_OFFSET + $order;
    }

    /** The id comes back in the label, so a test can see the note names the option that was written. */
    public function labelFor(int $elementValueId): ?string
    {
        return "optie {$elementValueId}";
    }
}
