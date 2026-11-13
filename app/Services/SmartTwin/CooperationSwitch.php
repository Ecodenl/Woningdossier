<?php

namespace App\Services\SmartTwin;

use App\Events\SmartTwinDisabledForCooperation;
use App\Events\SmartTwinEnabledForCooperation;
use App\Models\Cooperation;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Models\Audit;

/**
 * Switches SmartTwin on or off for a cooperation: the one way that switch is meant to move.
 *
 * Besides the column it leaves a row in the audit table, so who flipped it and when can be traced.
 * Cooperation itself is not auditable, and making it so for this one switch would audit every other
 * change to a cooperation along with it. Like every other audit row, it leaves the IP address and the
 * user agent out (see App\Resolvers\Audit).
 */
class CooperationSwitch
{
    public static function set(Cooperation $cooperation, bool $enabled): void
    {
        if ($cooperation->smarttwin_enabled === $enabled) {
            return;
        }

        $cooperation->update(['smarttwin_enabled' => $enabled]);

        self::audit($cooperation, $enabled);

        $enabled
            ? SmartTwinEnabledForCooperation::dispatch($cooperation)
            : SmartTwinDisabledForCooperation::dispatch($cooperation);
    }

    private static function audit(Cooperation $cooperation, bool $enabled): void
    {
        if (! config('audit.enabled', true)) {
            return;
        }

        $account = Auth::user();

        Audit::create([
            'user_type'      => $account?->getMorphClass(),
            'user_id'        => $account?->getAuthIdentifier(),
            'event'          => $enabled ? 'smarttwin-enabled' : 'smarttwin-disabled',
            'auditable_type' => $cooperation->getMorphClass(),
            'auditable_id'   => $cooperation->getKey(),
            'old_values'     => ['smarttwin_enabled' => ! $enabled],
            'new_values'     => ['smarttwin_enabled' => $enabled],
            'url'            => App::runningInConsole() ? 'console' : request()->fullUrl(),
        ]);
    }
}
