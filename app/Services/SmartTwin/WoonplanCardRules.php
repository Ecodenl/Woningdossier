<?php

namespace App\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;
use App\Services\UserActionPlanAdviceService;

/**
 * What a card in a SmartTwin woonplan may do: where it may go, and whether it may be thrown away.
 *
 * Only after the switch (Hoomdossier::smartTwinOwnsWoonplanFor()); before it every card moves as it
 * always did. The rules follow the woonplan design "Pagina 2", which is leading for them, and the
 * stuurgroep:
 *
 *   package    SmartTwin's chosen package. Starts in "Nu aanpakken" and moves freely between it and
 *              "Later uitvoeren" (stuurgroep): the package stays intact, only the costs under the
 *              columns follow the card. May be thrown away; without it the package no longer holds,
 *              and the woonplan offers a new calculation in place of SmartTwin's figures
 *              (SmartTwinPackage::isIntactIn()). Another package means going back to SmartTwin.
 *   possible   a measure SmartTwin offers but the package does not hold, "Later uitvoeren".
 *              Reordered within its column; may be thrown away.
 *   in-order   a card SmartTwin's import made for what is in good shape. Stays where it is.
 *   executed   a measure the resident carried out. Stays in "In orde"; taken back through its popup.
 *   own        the resident's or the cooperation's own measure, and the small measures (Patrick,
 *              15 October 2026). Moves freely between "Nu aanpakken" and "Later uitvoeren"; may be
 *              thrown away.
 *
 * Whether a SmartTwin card is package or possible follows from the package, not from its column: a
 * package card moved to "Later uitvoeren" is still a package card, and may come back.
 *
 * Moving a card into "In orde" is not a move: it marks the measure as carried out, which asks for a
 * date and a description first. That answer is EXECUTE, for the woonplan to open the popup.
 */
final class WoonplanCardRules
{
    public const PACKAGE = 'package';
    public const POSSIBLE = 'possible';
    public const IN_ORDER = 'in-order';
    public const EXECUTED = 'executed';
    public const OWN = 'own';

    public const ALLOWED = 'allowed';
    public const DENIED = 'denied';
    public const EXECUTE = 'execute';

    /**
     * @param  array<int, int>  $packageMeasureIds  The measures of the building's package
     *                                              (SmartTwinPackage::$measure_application_ids).
     */
    public static function kindOf(UserActionPlanAdvice $advice, array $packageMeasureIds): string
    {
        if ($advice->isExecuted()) {
            return self::EXECUTED;
        }

        if (AdviceSource::SMART_TWIN !== $advice->source) {
            return self::OWN;
        }

        if (UserActionPlanAdviceService::CATEGORY_COMPLETE === $advice->category) {
            return self::IN_ORDER;
        }

        $inPackage = MeasureApplication::class === $advice->user_action_plan_advisable_type
            && in_array((int) $advice->user_action_plan_advisable_id, array_map('intval', $packageMeasureIds), true);

        return $inPackage ? self::PACKAGE : self::POSSIBLE;
    }

    public static function move(string $kind, string $from, string $to): string
    {
        if ($from === $to) {
            // The order within a column is the resident's, whatever the card.
            return self::ALLOWED;
        }

        if (in_array($kind, [self::IN_ORDER, self::EXECUTED], true)) {
            return self::DENIED;
        }

        if (UserActionPlanAdviceService::CATEGORY_COMPLETE === $to) {
            return self::EXECUTE;
        }

        return in_array($kind, [self::PACKAGE, self::OWN], true) ? self::ALLOWED : self::DENIED;
    }

    public static function mayTrash(string $kind): bool
    {
        return in_array($kind, [self::PACKAGE, self::POSSIBLE, self::OWN], true);
    }

    /**
     * The columns a card of this kind may be dropped in from the given one, executing included, for
     * the browser to refuse the rest before the server has to.
     *
     * @return array<int, string>
     */
    public static function targets(string $kind, string $from): array
    {
        return array_values(array_filter(
            UserActionPlanAdviceService::getCategories(),
            fn (string $to) => self::DENIED !== self::move($kind, $from, $to),
        ));
    }
}
