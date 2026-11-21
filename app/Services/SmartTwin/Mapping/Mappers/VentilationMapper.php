<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Elements\PartList;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The ventilation of the dwelling as it is (decision 2026-11-07-smarttwin-installaties-mapping, N1–N3).
 *
 * SmartTwin names the system by its NTA 8800 variant, e.g. `C1`. Its letter says how air comes in
 * and goes out, which ventilation-type asks. B (mechanical supply, natural exhaust) has no option;
 * it becomes Mechanische ventilatie, which in Hoomdossier means mechanical exhaust: an approximation,
 * put before the stuurgroep. Whether the ventilation is demand driven is in the digit and the
 * subletter, which only NTA 8800 explains and SmartTwin has been asked for, so
 * ventilation-demand-driven is left alone. A dot in the code, as in one early response (`A.1`), is
 * ignored. With more than one system the worst wins.
 */
class VentilationMapper extends InstallationMapper
{
    /** The NTA letter => the order of the house-ventilation service value, worst first. */
    private const TYPES = [
        'A' => 1, // Natuurlijke ventilatie
        'B' => 2, // Mechanische ventilatie: an approximation, see above
        'C' => 2, // Mechanische ventilatie
        'D' => 3, // Gebalanceerde ventilatie
        'E' => 4, // Decentrale mechanische ventilatie
    ];

    protected function fields(): array
    {
        return [
            'ventilationSystems' => [
                'systemType'      => 'telt mee in ventilation-type; vraaggestuurd blijft leeg tot SmartTwin de codes uitlegt',
                'hasHeatRecovery' => 'telt mee in ventilation-heat-recovery',
                'description'     => 'niet gebruikt',
                'id'              => 'niet gebruikt',
            ],
        ];
    }

    protected function current(array $response): array
    {
        $systems = $this->systems($response, self::CURRENT, 'ventilationSystems');

        if (0 === $systems->count()) {
            return [
                MappingResult::cleared('ventilation-type', 'geen ventilatiesysteem in de response'),
                MappingResult::cleared('ventilation-heat-recovery', 'geen ventilatiesysteem in de response'),
            ];
        }

        return [$this->type($systems), $this->heatRecovery($systems)];
    }

    private function type(PartList $systems): MappingResult
    {
        $orders = [];

        foreach ($systems->parts as $system) {
            $code = str_replace('.', '', (string) ($system['systemType'] ?? ''));
            $order = self::TYPES[substr($code, 0, 1)] ?? null;

            if (is_null($order)) {
                return MappingResult::specViolation("systemType '{$code}' staat niet in de spec");
            }

            $orders[$code] = $order;
        }

        $order = min($orders);
        $code = array_search($order, $orders, true);
        $id = $this->elementValues->serviceValueIdForOrder('house-ventilation', $order);

        if (is_null($id)) {
            return MappingResult::targetMissing('ventilation-type', $order, "geen ventilatiesoort met order {$order}");
        }

        $note = str_starts_with($code, 'B')
            ? "systemType {$code}: mechanische toevoer en natuurlijke afvoer, benaderd als Mechanische ventilatie"
            : "systemType {$code}";

        return MappingResult::mapped('ventilation-type', $id, $note);
    }

    private function heatRecovery(PartList $systems): MappingResult
    {
        $values = array_map(fn (array $system) => $system['hasHeatRecovery'] ?? null, $systems->parts);

        if (in_array(null, $values, true)) {
            return MappingResult::cleared('ventilation-heat-recovery', 'hasHeatRecovery ontbreekt');
        }

        // The worst wins: heat recovery only when every system has it.
        $all = ! in_array(false, $values, true);

        return MappingResult::mapped('ventilation-heat-recovery', (int) $all, 'hasHeatRecovery');
    }
}
