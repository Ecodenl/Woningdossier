<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Enums\GlassType;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Elements\PartList;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The windows, doors and panels of the dwelling as it is, as the customer laid it out on
 * 7 November 2026 (decision 2026-11-07-smarttwin-glasmapping).
 *
 * Windows sit in facades and roofs alike, and count by their `area`: glass and frame together.
 * - total-window-surface: the area of all windows.
 * - current-living-rooms-windows: the glass type covering the most area. SmartTwin does not say
 *   which window belongs to which room, so current-sleeping-rooms-windows gets the same.
 * - frame-type: the frame type covering the most area, as one of SmartTwin's frame types.
 * Doors give their total surface and the insulation covering the most of it, panels the same plus
 * their frame. On a tie the worst wins, so an answer never makes the dwelling better than it is.
 *
 * Every answer is decided at one leaf, the anchor (ElementAnchor): the first window, door or panel
 * field of the current situation, wherever it sits. A part the response no longer lists is emptied.
 * The scenario is not mapped: it describes the dwelling after the advice, which the details on a
 * card show (ElementReader).
 */
class GlazingMapper implements FieldMapper
{
    private const CURRENT = 'current';
    private const SCENARIO = 'scenario';

    private const ASSEMBLIES = ['facadeAssemblies', 'roofAssemblies'];

    /** What each field counts towards, or why it is not used, for the report; by kind of part. */
    private const NOTES = [
        'windows' => [
            'area'          => 'telt mee in total-window-surface, en als gewicht in current-living-rooms-windows en frame-type',
            'glassType'     => 'telt mee in current-living-rooms-windows en current-sleeping-rooms-windows',
            'frameType'     => 'telt mee in frame-type',
            'frameMaterial' => 'niet gebruikt: frameType geeft het kozijn',
            'glassArea'     => 'niet gebruikt: area (glas plus kozijn) telt',
            'uValue'        => 'niet gebruikt: glassType geeft de kwaliteit',
            'description'   => 'niet gebruikt',
            'id'            => 'niet gebruikt',
        ],
        'doors' => [
            'area'                     => 'telt mee in total-door-surface, en als gewicht in door-quality',
            'insulationInConstruction' => 'telt mee in door-quality',
            'uValue'                   => 'niet gebruikt: insulationInConstruction geeft de kwaliteit',
            'description'              => 'niet gebruikt',
            'id'                       => 'niet gebruikt',
        ],
        'panels' => [
            'area'              => 'telt mee in total-panel-surface, en als gewicht in panel-quality en panel-frame-type',
            'insulationPresent' => 'telt mee in panel-quality',
            'frameType'         => 'telt mee in panel-frame-type',
            'uValue'            => 'niet gebruikt: insulationPresent geeft de kwaliteit',
            'description'       => 'niet gebruikt',
            'id'                => 'niet gebruikt',
        ],
    ];

    /** SmartTwin's glassType. */
    private const GLASS_TYPES = [
        0 => GlassType::TRIPLE,
        1 => GlassType::HR_PLUS_PLUS,
        2 => GlassType::HR_PLUS,
        3 => GlassType::HR,
        4 => GlassType::DOUBLE,
        5 => GlassType::SINGLE,
        6 => GlassType::VACUUM,
    ];

    /**
     * SmartTwin's window frameType => the order of the frames' option, see ElementsValuesTableSeeder.
     * SmartTwin numbers them from good to worse, which a tie goes by.
     */
    private const FRAME_TYPES = [
        0 => 5, // Hout
        1 => 6, // Kunststof
        2 => 7, // Aluminium thermisch gescheiden
        3 => 8, // Aluminium niet thermisch gescheiden
    ];

    /** insulationInConstruction => door-quality, worst first. */
    private const DOOR_QUALITY = [false => 'not-insulated', true => 'insulated'];

    /** SmartTwin's insulationPresent => panel-quality, worst first. */
    private const PANEL_QUALITY = [1 => 'not-insulated', 2 => 'unknown', 0 => 'insulated'];

    /** SmartTwin's panel frameType => panel-frame-type, worst first. */
    private const PANEL_FRAME_TYPES = [2 => 'metal', 1 => 'metal-thermally-broken', 0 => 'wood-or-plastic'];

    public function __construct(private readonly ElementValues $elementValues)
    {
    }

    public function paths(): array
    {
        $paths = [];

        foreach ([self::CURRENT, self::SCENARIO] as $section) {
            foreach (self::ASSEMBLIES as $assemblies) {
                foreach (self::NOTES as $kind => $fields) {
                    // The empty list, for an assembly without any.
                    $paths[] = "{$section}.properties.{$assemblies}.*.{$kind}";

                    foreach (array_keys($fields) as $field) {
                        $paths[] = "{$section}.properties.{$assemblies}.*.{$kind}.*.{$field}";
                    }
                }
            }
        }

        return $paths;
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult|array
    {
        $current = array_values(array_filter($this->paths(), fn (string $path) => str_starts_with($path, self::CURRENT . '.')));

        if (ElementAnchor::is($leaf, $response, self::CURRENT . '.properties', $current)) {
            return $this->answers($response);
        }

        return MappingResult::skipped($this->noteFor($leaf));
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array<int, MappingResult>
     */
    private function answers(array $response): array
    {
        $windows = $this->parts($response, 'windows');
        $doors = $this->parts($response, 'doors');
        $panels = $this->parts($response, 'panels');

        return [
            $this->surface('total-window-surface', $windows, 'ramen'),
            ...$this->glass($windows),
            $this->frameType($windows),
            $this->surface('total-door-surface', $doors, 'deuren'),
            $this->option('door-quality', $doors, 'insulationInConstruction', self::DOOR_QUALITY, 'deuren'),
            $this->surface('total-panel-surface', $panels, 'panelen'),
            $this->option('panel-quality', $panels, 'insulationPresent', self::PANEL_QUALITY, 'panelen'),
            $this->option('panel-frame-type', $panels, 'frameType', self::PANEL_FRAME_TYPES, 'panelen'),
        ];
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function parts(array $response, string $kind): PartList
    {
        return PartList::inAssemblies($response, self::CURRENT, array_fill_keys(self::ASSEMBLIES, $kind));
    }

    private function surface(string $target, PartList $parts, string $what): MappingResult
    {
        if (0 === $parts->count()) {
            return MappingResult::cleared($target, "geen {$what} in de response");
        }

        return MappingResult::mapped($target, $parts->sum('area'), "som van {$parts->count()} {$what}");
    }

    /**
     * @return array<int, MappingResult>
     */
    private function glass(PartList $windows): array
    {
        $targets = [
            'current-living-rooms-windows'   => 'living-rooms-windows',
            'current-sleeping-rooms-windows' => 'sleeping-rooms-windows',
        ];

        if (0 === $windows->count()) {
            return array_map(fn (string $target) => MappingResult::cleared($target, 'geen ramen in de response'), array_keys($targets));
        }

        $code = $windows->dominant('glassType', 'area', fn (mixed $code) => (self::GLASS_TYPES[$code] ?? null)?->rank() ?? -1);

        if (is_null($code)) {
            return array_map(fn (string $target) => MappingResult::cleared($target, 'glassType ontbreekt bij alle ramen'), array_keys($targets));
        }

        $glassType = self::GLASS_TYPES[$code] ?? null;

        if (! $glassType instanceof GlassType) {
            return [MappingResult::specViolation("glassType {$this->describe($code)} staat niet in de spec")];
        }

        $results = [];
        foreach ($targets as $target => $elementShort) {
            $elementValueId = $this->elementValues->idForOrder($elementShort, $glassType->value);

            $results[] = is_null($elementValueId)
                // The seeder has not run on this database: a deploy that went wrong.
                ? MappingResult::targetMissing($target, $glassType->value, "geen element value met order {$glassType->value} voor {$elementShort}")
                : MappingResult::mapped($target, $elementValueId, "glassType {$code} ({$this->elementValues->labelFor($elementValueId)}) over het grootste oppervlak van {$windows->count()} ramen");
        }

        return $results;
    }

    private function frameType(PartList $windows): MappingResult
    {
        if (0 === $windows->count()) {
            return MappingResult::cleared('frame-type', 'geen ramen in de response');
        }

        $code = $windows->dominant('frameType', 'area', fn (mixed $code) => -(int) $code);

        if (is_null($code)) {
            return MappingResult::cleared('frame-type', 'frameType ontbreekt bij alle ramen');
        }

        $order = self::FRAME_TYPES[$code] ?? null;

        if (is_null($order)) {
            return MappingResult::specViolation("frameType {$this->describe($code)} staat niet in de spec");
        }

        $elementValueId = $this->elementValues->idForOrder('frames', $order);

        if (is_null($elementValueId)) {
            return MappingResult::targetMissing('frame-type', $order, "geen element value met order {$order} voor frames");
        }

        return MappingResult::mapped('frame-type', $elementValueId, "frameType {$code} ({$this->elementValues->labelFor($elementValueId)}) over het grootste oppervlak");
    }

    /**
     * An answer that is one of SmartTwin's values, as the custom value it maps to.
     *
     * @param  array<int|string, string>  $answers  SmartTwin's value => our answer, worst first.
     */
    private function option(string $target, PartList $parts, string $field, array $answers, string $what): MappingResult
    {
        if (0 === $parts->count()) {
            return MappingResult::cleared($target, "geen {$what} in de response");
        }

        $ranks = array_flip(array_keys($answers));
        $value = $parts->dominant($field, 'area', fn (mixed $value) => $ranks[$this->key($value)] ?? -1);

        if (is_null($value)) {
            return MappingResult::cleared($target, "{$field} ontbreekt bij alle {$what}");
        }

        $answer = $answers[$this->key($value)] ?? null;

        if (is_null($answer)) {
            return MappingResult::specViolation("{$field} {$this->describe($value)} staat niet in de spec");
        }

        return MappingResult::mapped($target, $answer, "{$field} {$this->describe($value)} over het grootste oppervlak van {$parts->count()} {$what}");
    }

    /** A value as an array key: true and false become 1 and 0, as PHP does with them. */
    private function key(mixed $value): int|string
    {
        return is_bool($value) ? (int) $value : $value;
    }

    private function describe(mixed $value): string
    {
        return var_export($value, true);
    }

    private function noteFor(Leaf $leaf): string
    {
        if (str_starts_with($leaf->path, self::SCENARIO . '.')) {
            return 'niet gebruikt: het scenario beschrijft de woning na het advies, zie de details op de kaart';
        }

        foreach (self::NOTES as $kind => $fields) {
            foreach ($fields as $field => $note) {
                if (str_ends_with($leaf->pathGroup, ".{$kind}.*.{$field}")) {
                    return $note;
                }
            }
        }

        return 'beslist bij het eerste raam, de eerste deur of het eerste paneel';
    }
}
