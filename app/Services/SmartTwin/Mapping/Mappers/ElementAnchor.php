<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\ResponseFlattener;

/**
 * The one leaf that carries every answer about a part of the dwelling.
 *
 * An import takes over the whole situation SmartTwin describes, which includes emptying answers it
 * no longer supports — a crawlspace that is gone, a facade nothing insulates any more. Those have no
 * leaf of their own to hang off: there is nothing in the response for them. So a mapper decides all
 * its answers at one leaf that always exists while the element is in the response: the first of the
 * leaves it claims under the element's branch. That is the first floor's first field when there are
 * floors, the empty list when an assembly has none, and the empty assemblies list itself when the
 * dwelling has none at all.
 */
final class ElementAnchor
{
    /**
     * @param  array<string, mixed>  $response
     * @param  string                $prefix   The element's branch, e.g. `current.properties.floorAssemblies`.
     * @param  array<int, string>    $claimed  The path groups the mapper claims.
     */
    public static function is(Leaf $leaf, array $response, string $prefix, array $claimed): bool
    {
        $claimed = array_flip($claimed);

        foreach ((new ResponseFlattener())->flatten($response) as $candidate) {
            $underPrefix = $candidate->path === $prefix || str_starts_with($candidate->path, "{$prefix}.");

            if ($underPrefix && isset($claimed[$candidate->pathGroup])) {
                return $candidate->path === $leaf->path;
            }
        }

        return false;
    }
}
