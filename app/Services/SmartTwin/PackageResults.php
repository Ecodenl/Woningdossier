<?php

namespace App\Services\SmartTwin;

use App\Helpers\NumberFormatter;
use App\Models\SmartTwinPackage;

/**
 * What the woonplan shows under its columns once it is SmartTwin's: "Gekozen pakket".
 *
 * Every card in "Nu aanpakken" is a line with its name and costs: the package first, then the own
 * and small measures, each on a line of its own (Patrick, 15 October 2026). They all count in the
 * total. SmartTwin's amounts are never rounded (Patrick, 15 October 2026): not here, not when
 * shown, and the sums add up without floating point noise. What SmartTwin calculated (saving, subsidy, label, use, CO2) is for the package as a whole,
 * so it is only there while the woonplan still holds the package; otherwise the woonplan asks for a
 * new calculation instead (woonplan design "Pagina 2").
 */
final readonly class PackageResults
{
    /**
     * @param  array<int, array{name: string, from: float|null, to: float|null, amount: float, package: bool}>  $lines
     * @param  array<int, array{name: string|null, amount: float|null}>  $subsidies
     */
    public function __construct(
        public array $lines,
        public float $total,
        public bool $intact,
        public ?float $savingsPerYear,
        public ?float $subsidy,
        public array $subsidies,
        public ?string $labelNow,
        public ?string $labelAfter,
        public ?float $gasNow,
        public ?float $gasAfter,
        public ?float $electricityNow,
        public ?float $electricityAfter,
        public ?float $co2Savings,
    )
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $nowCards  The cards in "Nu aanpakken", in their order.
     */
    public static function from(?SmartTwinPackage $package, bool $intact, array $nowCards): self
    {
        $lines = [];
        foreach ($nowCards as $card) {
            // The costs as stored, not the card's: a card rounds to steps of five, a line does not.
            $costs = $card['exact_costs'] ?? $card['costs'];
            $from = $costs['from'] ?? null;
            $to = $costs['to'] ?? null;

            $lines[] = [
                'name'    => (string) $card['name'],
                'from'    => is_null($from) ? null : (float) $from,
                'to'      => is_null($to) ? null : (float) $to,
                'amount'  => self::amountOf($from, $to),
                'package' => WoonplanCardRules::PACKAGE === ($card['kind'] ?? null),
            ];
        }

        // The package first, then the own and small measures; within each, the order of the column.
        usort($lines, fn (array $a, array $b) => $b['package'] <=> $a['package']);

        $now = $package->current_result ?? [];
        $after = $package->scenario_result ?? [];
        $number = fn (array $result, string $field): ?float => is_numeric($result[$field] ?? null) ? (float) $result[$field] : null;

        $co2Now = $number($now, 'totalCo2Emission');
        $co2After = $number($after, 'totalCo2Emission');
        $savingsPerMonth = $package?->energy_cost_savings_per_month;

        return new self(
            lines: $lines,
            total: self::sum(array_column($lines, 'amount')),
            intact: $package instanceof SmartTwinPackage && $intact,
            savingsPerYear: is_null($savingsPerMonth) ? null : (float) bcmul(NumberFormatter::plain((float) $savingsPerMonth), '12', self::scaleOf([(float) $savingsPerMonth])),
            subsidy: is_null($package?->total_subsidy) ? null : (float) $package->total_subsidy,
            subsidies: $package->subsidies ?? [],
            labelNow: $now['energyLabel'] ?? null,
            labelAfter: $after['energyLabel'] ?? null,
            gasNow: $number($now, 'energyConsumptionGas'),
            gasAfter: $number($after, 'energyConsumptionGas'),
            electricityNow: $number($now, 'energyConsumptionElectricity'),
            electricityAfter: $number($after, 'energyConsumptionElectricity'),
            co2Savings: is_null($co2Now) || is_null($co2After) ? null : $co2Now - $co2After,
        );
    }

    /**
     * What a card counts for in the total, as the woonplan always counted it: the one price it has,
     * or the middle of its range.
     */
    private static function amountOf(mixed $from, mixed $to): float
    {
        $from = (float) ($from ?? 0);
        $to = (float) ($to ?? 0);

        return match (true) {
            $from > 0 && $to > 0 => (float) bcdiv(bcadd(NumberFormatter::plain($from), NumberFormatter::plain($to), self::scaleOf([$from, $to])), '2', self::scaleOf([$from, $to]) + 1),
            $to > 0              => $to,
            $from > 0            => $from,
            default              => 0.0,
        };
    }

    /**
     * Adds up without the noise of floating point: 0.1 + 0.2 is 0.3 here, not 0.30000000000000004,
     * and SmartTwin's amounts are shown unrounded, noise included.
     *
     * @param  array<int, float>  $amounts
     */
    private static function sum(array $amounts): float
    {
        $total = '0';
        foreach ($amounts as $amount) {
            $total = bcadd($total, NumberFormatter::plain($amount), self::scaleOf($amounts));
        }

        return (float) $total;
    }

    /**
     * The most decimals any of these amounts has.
     *
     * @param  array<int, float>  $amounts
     */
    private static function scaleOf(array $amounts): int
    {
        return max(0, ...array_map(NumberFormatter::decimalsOf(...), $amounts));
    }
}
