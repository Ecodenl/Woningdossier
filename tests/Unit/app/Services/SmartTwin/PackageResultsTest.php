<?php

namespace Tests\Unit\app\Services\SmartTwin;

use App\Models\SmartTwinPackage;
use App\Services\SmartTwin\PackageResults;
use App\Services\SmartTwin\WoonplanCardRules;
use Tests\TestCase;

/**
 * "Gekozen pakket": a line per card in "Nu aanpakken", the package first, all of them in the total;
 * SmartTwin's figures only while the package holds. The figures are those of dossier 100271.
 *
 * On the Laravel TestCase rather than PHPUnit's: the package is an Eloquent model.
 */
final class PackageResultsTest extends TestCase
{
    private function package(): SmartTwinPackage
    {
        return new SmartTwinPackage([
            'measure_application_ids'       => [1, 2],
            'current_result'                => ['energyConsumptionGas' => 1500, 'energyConsumptionElectricity' => 2500, 'totalCo2Emission' => 5826.2365, 'energyLabel' => 'C'],
            'scenario_result'               => ['energyConsumptionGas' => 0, 'energyConsumptionElectricity' => 6035.9627, 'totalCo2Emission' => 1162.0925, 'energyLabel' => 'A+++'],
            'energy_cost_savings_per_month' => 210,
            'total_subsidy'                 => 6853.75,
            'subsidies'                     => [['name' => 'ISDE', 'amount' => 6853.75]],
        ]);
    }

    private function card(string $name, ?float $from, ?float $to, ?string $kind): array
    {
        return ['name' => $name, 'costs' => ['from' => $from, 'to' => $to], 'kind' => $kind];
    }

    public function test_every_card_in_now_is_a_line_with_the_package_first(): void
    {
        $results = PackageResults::from($this->package(), true, [
            $this->card('Zonnescherm', 1200, null, WoonplanCardRules::OWN),
            $this->card('Spouwmuurisolatie', null, 2450, WoonplanCardRules::PACKAGE),
            $this->card('Ledverlichting', null, null, WoonplanCardRules::OWN),
            $this->card('Vloerisolatie', 1000, 1200, WoonplanCardRules::PACKAGE),
        ]);

        $this->assertSame(['Spouwmuurisolatie', 'Vloerisolatie', 'Zonnescherm', 'Ledverlichting'], array_column($results->lines, 'name'));
        // A range counts for its middle; a card without costs for nothing.
        $this->assertSame([2450.0, 1100.0, 1200.0, 0.0], array_column($results->lines, 'amount'));
        $this->assertEqualsWithDelta(4750.0, $results->total, 0.001);
    }

    public function test_smart_twins_figures_while_the_package_holds(): void
    {
        $results = PackageResults::from($this->package(), true, []);

        $this->assertTrue($results->intact);
        $this->assertEqualsWithDelta(2520.0, $results->savingsPerYear, 0.001);
        $this->assertEqualsWithDelta(6853.75, $results->subsidy, 0.001);
        $this->assertSame(['C', 'A+++'], [$results->labelNow, $results->labelAfter]);
        $this->assertSame([1500.0, 0.0], [$results->gasNow, $results->gasAfter]);
        $this->assertEqualsWithDelta(4664.144, $results->co2Savings, 0.001);
    }

    public function test_a_broken_package_is_not_intact(): void
    {
        $this->assertFalse(PackageResults::from($this->package(), false, [])->intact);
        $this->assertFalse(PackageResults::from(null, true, [])->intact);
    }

    public function test_the_total_is_the_exact_sum(): void
    {
        // Two lines of € 100,50 show € 101 each; the total is the exact € 201, rounded once.
        $results = PackageResults::from($this->package(), true, [
            $this->card('Spouwmuurisolatie', null, 100.5, WoonplanCardRules::PACKAGE),
            $this->card('Vloerisolatie', null, 100.5, WoonplanCardRules::PACKAGE),
        ]);

        $this->assertSame(201.0, $results->total);
    }
}
