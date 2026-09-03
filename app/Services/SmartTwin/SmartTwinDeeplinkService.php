<?php

namespace App\Services\SmartTwin;

use App\Helpers\Models\BuildingSettingHelper;
use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\User;
use App\Services\SmartTwin\Api\SmartTwinApi;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;

/**
 * Orchestrates the synchronous SSO handoff into the SmartTwin tools.
 *
 * Unlike the account create/delete jobs (fire-and-forget), this runs in the
 * request/response cycle because the caller needs the deeplink + token *now*
 * to render the auto-submitting bridge form. The JWT is never persisted.
 *
 * The dossierId <-> building storage (needed to match incoming result webhooks to a
 * building, see SmartTwinController) is commented out below: the link endpoints never
 * return a dossierId (confirmed against both the test environment and the Advice API
 * OpenAPI spec — GetLinkToAdviceResponseModel / GetLinkToQuickscanResponseModel don't
 * have that field), so it was dead code. Left in place, commented, while the SSO
 * handoff itself is under test; the webhook-matching problem is tracked separately.
 */
class SmartTwinDeeplinkService
{
    public function __construct(private readonly SmartTwinApi $api)
    {
    }

    public function handoff(User $user, Building $building, string $roleName): HandoffResult
    {
        $smartTwinUserId = $user->account?->smartTwinUserId();
        if (empty($smartTwinUserId)) {
            return HandoffResult::notConfigured();
        }

        $payload = $this->buildAddressPayload($smartTwinUserId, $building);

        try {
            $token = $this->api->user()->login($smartTwinUserId)['accessToken'] ?? null;
            if (empty($token)) {
                Log::error('SmartTwin login returned no accessToken', [
                    'building_id' => $building->id,
                    'user_id'     => $user->id,
                ]);

                return HandoffResult::failed();
            }

            // The tool follows the current role: resident -> quickscan, coach -> advisor tool.
            // Any other role (coordinator, cooperation-admin, ...) is rejected rather than silently
            // treated as a resident. A user is not supposed to hold both resident and coach at once.
            $response = match ($roleName) {
                RoleHelper::ROLE_RESIDENT => $this->api->advice()->getQuickScanLink($payload),
                RoleHelper::ROLE_COACH    => $this->api->advice()->getAdvisorToolLink($payload),
                default                   => null,
            };
        } catch (BadResponseException $e) {
            // This runs in the request cycle, so an uncaught Guzzle exception would hit the user as
            // a 500 instead of a message on the woonplan. SmartTwin answers 4xx with a bare status
            // and no machine-readable reason, so the raw body is the only diagnostic we get.
            Log::error('SmartTwin rejected the deeplink handoff', [
                'building_id' => $building->id,
                'user_id'     => $user->id,
                'role'        => $roleName,
                'status'      => $e->getResponse()->getStatusCode(),
                'body'        => (string) $e->getResponse()->getBody(),
                'payload'     => $payload,
            ]);

            return HandoffResult::failed();
        }

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

        if (empty($url)) {
            // A 200 with an unexpected shape is otherwise indistinguishable from a network failure:
            // both end up as "er ging iets mis" on the woonplan. Log the body verbatim so we can
            // see which field SmartTwin actually sent.
            Log::error('SmartTwin returned an unusable deeplink response', [
                'building_id' => $building->id,
                'role'        => $roleName,
                'response'    => $response,
            ]);

            return HandoffResult::failed();
        }

        // Disabled: the link endpoints never return a dossierId (see class docblock), so this
        // check always failed / this store never ran. Kept for when SmartTwin starts sending one.
        // $dossierId = $response['dossierId'] ?? null;
        // if (! empty($dossierId)) {
        //     // Store the dossierId <-> building link so the webhook (SmartTwinController)
        //     // can resolve this building when results come back.
        //     BuildingSettingHelper::syncSettings($building, [
        //         BuildingSettingHelper::SHORT_SMARTTWIN_DOSSIER_ID => $dossierId,
        //     ]);
        // }

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
            'async'               => true,
        ];
    }
}
