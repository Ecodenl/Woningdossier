<?php

namespace App\Helpers;

use App\Models\Account;
use App\Models\Client;
use App\Models\InputSource;
use App\Models\User;
use App\Scopes\GetValueScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class Hoomdossier
{
    /** @var int the length the password should be */
    const PASSWORD_LENGTH = 8;

    public static function hasEnabledEconobisCalls(): bool
    {
        return config('hoomdossier.services.econobis.enabled', false);
    }

    /**
     * @deprecated
     * Return the most credible value from a given collection.
     */
    public static function getMostCredibleValueFromCollection(Collection $results, string $column)
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
     */
    public static function getMostCredibleValue(Relation $relation, string $column): mixed
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $found = $relation->allInputSources()->where('input_source_id', $masterInputSource->id)->get();

        return self::getMostCredibleValueFromCollection($found, $column);
    }

    /**
     * Return the most credible input source (short) for a relationship.
     */
    public static function getMostCredibleInputSource(Relation $relation): ?string
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
     */
    public static function user(): ?User
    {
        return (static::account() instanceof Account) ? static::account()->user() : null;
    }

    /**
     * Returns the current account.
     */
    public static function account(): null|Account|Client
    {
        return Auth::user();
    }

    public static function getSupportedLocales(): array
    {
        return config('hoomdossier.supported_locales');
    }
}
