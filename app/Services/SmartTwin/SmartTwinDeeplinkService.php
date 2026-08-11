<?php

namespace App\Services\SmartTwin;

use App\Helpers\Models\BuildingSettingHelper;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\User;
use App\Services\SmartTwin\Api\SmartTwinApi;

/**
 * Orchestrates the synchronous SSO handoff into the SmartTwin tools.
 *
 * Unlike the account create/delete jobs (fire-and-forget), this runs in the
 * request/response cycle because the caller needs the deeplink + token *now*
 * to render the auto-submitting bridge form. The JWT is never persisted; only
 * the dossierId is stored so incoming webhooks can be matched to a building.
 */
class SmartTwinDeeplinkService
{
    public function __construct(private readonly SmartTwinApi $api)
    {
    }

    public function handoff(User $user, Building $building, string $roleName): HandoffResult
    {
        $smartTwinUserId = $user->extra['smarttwin_user_id'] ?? null;
        if (empty($smartTwinUserId)) {
            return HandoffResult::notConfigured();
        }

        $token = $this->api->user()->login($smartTwinUserId)['accessToken'] ?? null;
        if (empty($token)) {
            return HandoffResult::failed();
        }

        $payload = $this->buildAddressPayload($smartTwinUserId, $building);

        // The tool follows the current role: resident -> quickscan, coach -> advisor tool.
        // Any other role (coordinator, cooperation-admin, ...) is rejected rather than silently
        // treated as a resident. A user is not supposed to hold both resident and coach at once.
        $response = match ($roleName) {
            RoleHelper::ROLE_RESIDENT => $this->api->advice()->getQuickScanLink($payload),
            RoleHelper::ROLE_COACH    => $this->api->advice()->getAdvisorToolLink($payload),
            default                   => null,
        };

        if ($response === null) {
            return HandoffResult::unsupportedRole();
        }

        if ($roleName === RoleHelper::ROLE_COACH) {
            // AdviceSessionError: 1 = AdviceInProgressByOtherUser
            if ((int) ($response['error'] ?? 0) === 1) {
                return HandoffResult::adviceInProgress();
            }

            $url = $response['adviceUrl'] ?? null;
        } else {
            $url = $response['quickScanUrl'] ?? null;
        }

        $dossierId = $response['dossierId'] ?? null;
        if (empty($url) || empty($dossierId)) {
            return HandoffResult::failed();
        }

        // Store the dossierId <-> building link so the webhook (SmartTwinController)
        // can resolve this building when results come back.
        BuildingSettingHelper::syncSettings($building, [
            BuildingSettingHelper::SHORT_SMARTTWIN_DOSSIER_ID => $dossierId,
        ]);

        return HandoffResult::success($url, $token);
    }

    private function buildAddressPayload(string $smartTwinUserId, Building $building): array
    {
        return [
            'userId'              => $smartTwinUserId,
            'postalCode'          => $building->postal_code,
            'houseNumber'         => (int) $building->number,
            'houseNumberAddition' => $building->extension !== '' ? $building->extension : null,
            // Results are delivered asynchronously via the event/webhook flow; this
            // also enables the token-in-body POST handoff on the returned URL.
            //'async'               => true,
            'async'               => false,
        ];
    }
}
