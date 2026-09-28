<?php

namespace App\Services\SmartTwin\Mapping;

/**
 * Turns a decoded SmartTwin response into a flat list of leaves.
 *
 * Knows nothing about mapping: it is the input side of the walk, and stays testable on its own.
 */
class ResponseFlattener
{
    /**
     * @param  array<string, mixed>  $response
     * @return array<int, Leaf>
     */
    public function flatten(array $response): array
    {
        $leaves = [];
        $this->walk($response, '', '', $leaves);

        return $leaves;
    }

    /**
     * @param  array<int, Leaf>  $leaves
     */
    private function walk(mixed $value, string $path, string $pathGroup, array &$leaves): void
    {
        // An empty array is a leaf: "no roof assemblies were sent" is information, and dropping it
        // would silently shrink the report instead of reporting an empty field.
        if (! is_array($value) || empty($value)) {
            $leaves[] = new Leaf($path, $pathGroup, $value);

            return;
        }

        $isList = array_is_list($value);

        foreach ($value as $key => $child) {
            $this->walk(
                $child,
                $this->append($path, (string) $key),
                $this->append($pathGroup, $isList ? '*' : (string) $key),
                $leaves,
            );
        }
    }

    private function append(string $prefix, string $segment): string
    {
        return '' === $prefix ? $segment : "{$prefix}.{$segment}";
    }
}
