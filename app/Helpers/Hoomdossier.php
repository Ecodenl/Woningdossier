<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Relations\Relation;

class Hoomdossier
{
    public static function convertDecimal($input)
    {
        return str_replace(',', '.', $input);
    }

    public static function getMostCredibleValue(Relation $relation, $column, $default = null, $onlyReturnForInputSource = null)
    {
        $baseQuery = $relation
            ->withoutGlobalScope(GetValueScope::class)
            ->join('input_sources', $relation->getRelated()->getTable().'.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order', 'ASC');

        // if is not empty, we need to search the answers for a particular input source
        if (!is_null($onlyReturnForInputSource)) {
            $inputSourceToReturn = InputSource::findByShort($onlyReturnForInputSource);
            $found = $baseQuery->where('input_source_id', $inputSourceToReturn->id);
        } else {
            // if the $onlyReturnForInputSource is empty, the base query is enough
            $found = $baseQuery->get([$relation->getRelated()->getTable().'.*', 'input_sources.short']);
        }

        $results = $found->pluck($column, 'short');

        // if the column name contains 'surface' there is particular logic:
        // if $value <= 0 we don't return it. We just check next sources to
        // see if there's a proper value and return that.

        // additional there are some fields which are filled (with null) before
        // the user actually gets to that step (building_features fields)
        // these fields also get a 'fallthrough' via $fallthroughColumns
        $falltroughColumns = [
            'facade_plastered_painted',
            'facade_plastered_surface_id',
            'facade_damaged_paintwork_id',
            'wall_joints',
            'contaminated_wall_joints',
            'monument',
            'building_layers',
        ];

        // Always check my own input source first. If that is properly filled
        // return that.
        $myInputSource = HoomdossierSession::getInputSource(true);

        if ($results->has($myInputSource->short)) {
            $value = $results->get($myInputSource->short);

            if (false !== stristr($column, 'surface') && $value <= 0) {
                // skip this one
                $value = null;
            }
            if (in_array($column, $falltroughColumns) && is_null($value)) {
                // skip this one
                $value = null;
            }
            if (! is_null($value) && '' !== $value) {
                return $value;
            }
        }

        // .. My own input source was not (properly) filled.
        // Return the best match. Treat the results in order.
        foreach ($results as $inputSourceShort => $value) {
            if (false !== stristr($column, 'surface') && $value <= 0) {
                // skip this one
                continue;
            }
            if (in_array($column, $falltroughColumns) && is_null($value)) {
                // skip this one
                continue;
            }
            if (InputSource::RESIDENT_SHORT == $inputSourceShort) {
                // no matter what
                return $value;
            }
            if (! is_null($value) && '' !== $value) {
                return $value;
            }
        }
        // No value found
        return $default;
    }

    /**
     * Return the most credible input source for a relationship
     *
     * @param Relation $relation
     * @return int|mixed|null
     */
    public static function getMostCredibleInputSource(Relation $relation)
    {
        $found = $relation
            ->withoutGlobalScope(GetValueScope::class)
            ->join('input_sources', $relation->getRelated()->getTable().'.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order', 'ASC')
            ->get([$relation->getRelated()->getTable().'.*', 'input_sources.short']);

        $results = $found->pluck( 'short');

        // Always check my own input source first. If that is properly filled
        // return that.
        $myInputSource = HoomdossierSession::getInputSource(true);

        // if the results contain answers from me.
        if ($results->contains($myInputSource->short)) {
            return $myInputSource->short;
        }

        // my own inputsource is not available, so we return the most trust worthy / credible input source short
        foreach ($results as $inputSourceShort) {
            return $inputSourceShort;
        }
        return null;
    }

    /**
     * Returns the current user.
     *
     * @return \App\Models\User|null
     */
    public static function user()
    {
        return (static::account() instanceof Account) ? static::account()->user() : null;
    }

    /**
     * Returns the current account.
     *
     * @return \App\Models\Account|null
     */
    public static function account()
    {
        return \Auth::user();
    }
}
