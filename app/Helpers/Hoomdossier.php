<?php

namespace App\Helpers;

use App\Enums\AdviceSource;
use App\Models\Account;
use App\Models\Building;
use App\Models\Client;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
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
     * Whether this environment can talk to SmartTwin at all (SMARTTWIN_ENABLED). This is the switch
     * above the one per cooperation: what does not belong to a cooperation, such as the API client,
     * the solution catalogue and removing a SmartTwin user, only asks this.
     */
    public static function hasSmartTwinApi(): bool
    {
        return (bool) config('hoomdossier.services.smarttwin.enabled', false);
    }

    /**
     * Whether this cooperation takes part in SmartTwin. Everything that behaves differently for a
     * cooperation that does asks this, never the environment switch alone.
     */
    public static function smartTwinEnabledFor(?Cooperation $cooperation): bool
    {
        return $cooperation instanceof Cooperation
            && static::hasSmartTwinApi()
            && $cooperation->smarttwin_enabled;
    }

    /**
     * The cooperation that decides for this building: the one of the user it belongs to.
     *
     * Read past the CooperationScope on User. A webhook is a plain HTTP request without a session,
     * so the scope resolves to cooperation_id = 0 there and would hide the user altogether.
     */
    public static function cooperationOf(Building $building): ?Cooperation
    {
        $user = $building->relationLoaded('user') && $building->user instanceof User
            ? $building->user
            : $building->user()->forAllCooperations()->first();

        return $user?->cooperation;
    }

    /**
     * Whether SmartTwin fills the woonplan of this building. SmartTwin's check is a quick scan, so
     * a building without the quick scan (lite scan only) keeps the woonplan as it was without
     * SmartTwin, and is never sent to SmartTwin.
     */
    public static function smartTwinFillsWoonplanFor(Building $building): bool
    {
        if (! static::smartTwinEnabledFor(static::cooperationOf($building))) {
            return false;
        }

        $quickScan = Scan::quick();

        return $quickScan instanceof Scan
            && ScanAvailabilityHelper::isAvailableForBuilding($building, $quickScan);
    }

    /**
     * Whether the woonplan of this building has gone over to SmartTwin: its cooperation takes part
     * and SmartTwin advised the building at least once.
     *
     * This is THE switch for everything that works differently in a SmartTwin woonplan: the
     * calculation leaving the advices to SmartTwin, woonplan actions applying to every input source,
     * "uitgevoerd", the expert scan and the Verbeterjehuis subsidies. Ask this, not
     * smartTwinEnabledFor(), for any of those. A cooperation that switches SmartTwin on keeps
     * finished dossiers that will never go through SmartTwin, and their woonplan has to keep working
     * as it always did. Each building goes over with its own first SmartTwin advice instead.
     *
     * What has to work before that advice, such as hiding the questions SmartTwin answers or the
     * hand-off to SmartTwin, asks smartTwinEnabledFor() or smartTwinFillsWoonplanFor() instead.
     */
    public static function smartTwinOwnsWoonplanFor(Building $building): bool
    {
        return static::smartTwinEnabledFor(static::cooperationOf($building))
            && UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $building->user_id)
            ->where('source', AdviceSource::SMART_TWIN)
            ->exists();
    }

    /**
     * Whether SmartTwin drives the scan the building is being asked: its quick scan, where SmartTwin
     * fills the woonplan. The questions of such a scan come back from SmartTwin, so none of them is
     * required here (see ToolQuestion::isRequired()).
     */
    public static function smartTwinDrivesScan(Building $building, Scan $scan): bool
    {
        return $scan->isQuickScan() && static::smartTwinFillsWoonplanFor($building);
    }

    /**
     * Whether SmartTwin answers this step's questions for the building, so Hoomdossier does not ask
     * them: the steps SmartTwin provides in a cooperation that takes part (Step::isProvidedBySmartTwin()),
     * and their lite scan counterparts for a building SmartTwin fills the woonplan for. A building
     * with only the lite scan is never sent to SmartTwin and keeps being asked.
     */
    public static function smartTwinProvidesStepFor(Building $building, Step $step): bool
    {
        if ($step->isProvidedBySmartTwin()) {
            return static::smartTwinEnabledFor(static::cooperationOf($building));
        }

        return $step->mirrorsStepProvidedBySmartTwin() && static::smartTwinFillsWoonplanFor($building);
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
            ->join('input_sources', $relation->getRelated()->getTable() . '.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order');

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
            ->join('input_sources', $relation->getRelated()->getTable() . '.input_source_id', '=', 'input_sources.id')
            ->orderBy('input_sources.order')
            ->get([$relation->getRelated()->getTable() . '.*', 'input_sources.short']);

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
