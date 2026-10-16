<?php

namespace App\Services\SmartTwin\Mapping\Elements;

use Closure;

/**
 * Parts of a dwelling as SmartTwin lists them — windows, doors, systems — with the few sums every
 * element is described by: a total, a value weighted by surface, the value covering the most, and
 * lines grouping alike parts.
 *
 * The insulated elements have InsulatedParts, which the answers are calculated with too; this is
 * the same idea for everything else, so describing an element is choosing fields, not writing code.
 */
final class PartList
{
    /**
     * @param  array<int, array<string, mixed>>  $parts
     */
    public function __construct(public readonly array $parts)
    {
    }

    /**
     * Parts held by assemblies: `properties.<assemblies>.*.<key>.*`, for each of the given branches.
     *
     * @param  array<string, mixed>        $response
     * @param  array<string, string>       $branches  Assemblies key => parts key, e.g. `facadeAssemblies` => `windows`.
     */
    public static function inAssemblies(array $response, string $section, array $branches): self
    {
        $parts = [];

        foreach ($branches as $assemblies => $key) {
            foreach ($response[$section]['properties'][$assemblies] ?? [] as $assembly) {
                foreach ($assembly[$key] ?? [] as $part) {
                    $parts[] = $part;
                }
            }
        }

        return new self($parts);
    }

    /**
     * Systems listed straight under the properties: `properties.<key>.*`.
     *
     * @param  array<string, mixed>  $response
     */
    public static function systems(array $response, string $section, string $key): self
    {
        return new self(array_values($response[$section]['properties'][$key] ?? []));
    }

    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * @return array<int, string>
     */
    public function ids(): array
    {
        return array_values(array_filter(array_map(fn (array $part) => $part['id'] ?? null, $this->parts)));
    }

    public function sum(string $field): float
    {
        return round(array_sum(array_map(fn (array $part) => (float) ($part[$field] ?? 0), $this->parts)), 2);
    }

    /** The value weighted by surface, or null when there is no surface to weigh with. */
    public function weighted(string $field, string $by = 'area'): ?float
    {
        $total = $this->sum($by);

        if ($total <= 0) {
            return null;
        }

        $weighted = array_sum(array_map(
            fn (array $part) => (float) ($part[$field] ?? 0) * (float) ($part[$by] ?? 0),
            $this->parts,
        ));

        return round($weighted / $total, 2);
    }

    /**
     * The value covering the most surface (or the most parts, without a surface field). A tie goes
     * to the value seen first, which keeps the answer stable from one run to the next.
     */
    public function dominant(string $field, ?string $by = 'area'): mixed
    {
        $weights = [];
        $values = [];

        foreach ($this->parts as $part) {
            if (! array_key_exists($field, $part) || is_null($part[$field])) {
                continue;
            }

            $key = var_export($part[$field], true);
            $values[$key] = $part[$field];
            $weights[$key] = ($weights[$key] ?? 0) + (is_null($by) ? 1 : (float) ($part[$by] ?? 0));
        }

        if (empty($weights)) {
            return null;
        }

        return $values[array_search(max($weights), $weights, true)];
    }

    /**
     * Alike parts as one line each: grouped on `$keys`, with the surface and number of parts added up
     * and `$weighted` averaged by surface. Largest surface first.
     *
     * @param  array<string, string>  $keys      Line key => part field.
     * @param  array<string, string>  $weighted  Line key => part field.
     * @return array<int, array<string, mixed>>
     */
    public function lines(array $keys, array $weighted = []): array
    {
        $groups = [];

        foreach ($this->parts as $part) {
            $values = array_map(fn (string $field) => $part[$field] ?? null, $keys);
            $groups[serialize($values)][] = $part;
        }

        $lines = [];

        foreach ($groups as $parts) {
            $group = new self($parts);
            $line = array_map(fn (string $field) => $parts[0][$field] ?? null, $keys);

            foreach ($weighted as $key => $field) {
                $line[$key] = $group->weighted($field);
            }

            $lines[] = array_merge($line, ['area' => $group->sum('area'), 'count' => $group->count()]);
        }

        usort($lines, fn (array $a, array $b) => $b['area'] <=> $a['area']);

        return $lines;
    }

    /**
     * One line per part, with the chosen fields under their own names.
     *
     * @param  array<string, string|Closure>  $fields  Line key => part field, or a closure on the part.
     * @return array<int, array<string, mixed>>
     */
    public function each(array $fields): array
    {
        return array_map(
            fn (array $part) => array_map(fn ($field) => $field instanceof Closure ? $field($part) : ($part[$field] ?? null), $fields),
            $this->parts,
        );
    }
}
