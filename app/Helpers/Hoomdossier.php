<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Hoomdossier
{
    /** @var int the length the password should be */
    const PASSWORD_LENGTH = 8;

    /**
     * Check if a column contains a needle, wrapper for stristr.
     *
     * @return bool
     */
    public static function columnContains(string $column, string $needle)
    {
        // TODO: Refactor references to Str::contains
        return false !== stristr($column, $needle);
    }

    /**
     * Method to return a unit for a given column.
     *
     * @param $column
     *
     * @return mixed|string
     */
    public static function getUnitForColumn($column)
    {
        $unitsForCalculations = [
            'savings_gas' => 'm3',
            'savings_co2' => 'kg',
            'savings_money' => '€',
            'cost_indication' => '€',
            'costs' => '€',
            'm2' => 'm2',
            'yield_electricity' => 'kWh',
            'raise_own_consumption' => '%',
            'interest_comparable' => '%',
            'percentage_consumption' => '%',
        ];

        if (static::columnContains($column, 'surface') || static::columnContains($column, 'm2')) {
            $unit = 'm2';
        }

        if (static::columnContains($column, 'amount_electricity')) {
            $unit = 'kWh';
        }

        if (static::columnContains($column, 'amount_gas')) {
            $unit = 'm3';
        }

        if (static::columnContains($column, 'peak_power')) {
            $unit = 'Wp';
        }

        if (static::columnContains($column, 'angle')) {
            $unit = '°';
        }

        return $unit ?? $unitsForCalculations[$column] ?? '';
    }

    /**
     * Return the most credible value from a given collection.
     *
     * @param string $column
     * @param null   $default
     *
     * @return mixed|null
     */
    public static function getMostCredibleValueFromCollection(Collection $results, $column, $default = null)
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        return $results->pluck($column, 'input_source_id')->get($masterInputSource->id);
    }

    /**
     * Will return a collection ordered on the input source credibility.
     */
    public static function orderRelationShipOnInputSourceCredibility(Relation $relation): Relation
    {
        $baseQuery = $relation
            ->withoutGlobalScope(GetValueScope::class)
            ->join('input_sources', $relation->getRelated()->getTable().'.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order', 'ASC');

        return $baseQuery;
    }

    /**
     * Will return the most credible value from a given relationship.
     *
     * @param null $column
     * @param null $default
     * @param null $onlyReturnForInputSource
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|null
     */
    public static function getMostCredibleValue(Relation $relation, $column = null, $default = null, $onlyReturnForInputSource = null)
    {

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $found = $relation->allInputSources()->where('input_source_id', $masterInputSource->id)->get();

        return self::getMostCredibleValueFromCollection($found, $column, $default);
    }

    /**
     * Return the most credible input source for a relationship.
     *
     * @return int|mixed|null
     */
    public static function getMostCredibleInputSource(Relation $relation)
    {
        $found = $relation
            ->withoutGlobalScope(GetValueScope::class)
            ->join('input_sources', $relation->getRelated()->getTable().'.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order', 'ASC')
            ->get([$relation->getRelated()->getTable().'.*', 'input_sources.short']);

        $results = $found->pluck('short');

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
        return Auth::user();
    }
}
