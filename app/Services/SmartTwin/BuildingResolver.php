<?php

namespace App\Services\SmartTwin;

use App\Enums\SmartTwin\EventType;
use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\User;
use App\Services\AddressService;
use App\Services\BuildingCoachStatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Finds the building an incoming SmartTwin webhook is about.
 *
 * SmartTwin creates a new dossier every time someone enters their tools, so the DossierId in the
 * callback is a fresh value we have never seen before — it cannot be a lookup key. What the callback
 * does carry is the SmartTwin user id and the address, and the user id is stored on our account.
 *
 * So: account -> its users (one per cooperation, each with its own roles) -> the buildings those
 * users may reach -> the one at the address in the callback. That candidate set is also the
 * authorisation boundary. We never search outside it, because then a callback carrying nothing but
 * an address could write results into any dossier in the database.
 */
class BuildingResolver
{
    public function __construct(private readonly AddressService $addressService)
    {
    }

    /**
     * @param  array<string, mixed>  $data  The `data` object of the webhook payload.
     */
    public function resolve(EventType $eventType, array $data): ?Building
    {
        $smartTwinUserId = $data['UserId'] ?? null;

        if (empty($smartTwinUserId)) {
            Log::warning('SmartTwin webhook: no UserId in the payload', $data);

            return null;
        }

        $account = Account::where('smarttwin_user_id', $smartTwinUserId)->first();

        if (! $account instanceof Account) {
            Log::warning('SmartTwin webhook: no account for UserId', ['userId' => $smartTwinUserId]);

            return null;
        }

        $candidates = $this->candidatesFor($account, $eventType);

        if ($candidates->isEmpty()) {
            // Either the account holds no user with the role this flow belongs to, or the coach is
            // no longer attached to any building. Both are a refusal, not a lookup miss.
            Log::warning('SmartTwin webhook: account has no reachable buildings for this flow', [
                'account_id' => $account->getKey(),
                'flow'       => $eventType->value,
            ]);

            return null;
        }

        return $this->matchOnAddress($candidates, $data, $account, $eventType);
    }

    /**
     * The buildings this account may receive results for, for this flow. The event type tells us
     * which role did the work — the advisor tool is the coach flow, the quickscan the resident one —
     * so we only have to walk the users that hold that role.
     *
     * @return Collection<int, Building>
     */
    private function candidatesFor(Account $account, EventType $eventType): Collection
    {
        // A webhook is a plain HTTP request without a session, so the CooperationScope on User
        // resolves to cooperation_id = 0 and hides every user. It is registered whenever we are not
        // in the console, which is exactly here — and never under phpunit, so a test would pass
        // while production silently found nothing. Hence the explicit forAllCooperations().
        $users = $account->users()->forAllCooperations()->get();

        $buildings = new Collection();

        foreach ($users as $user) {
            $buildings = $buildings->merge(match ($eventType) {
                EventType::RESIDENT_SCAN_FINISHED => $this->residentBuildings($user),
                EventType::COACH_SCAN_FINISHED    => $this->coachBuildings($user),
            });
        }

        return $buildings->unique('id')->values();
    }

    /**
     * A resident has one building: their own home.
     *
     * @return Collection<int, Building>
     */
    private function residentBuildings(User $user): Collection
    {
        if (! $user->hasRole(RoleHelper::ROLE_RESIDENT)) {
            return new Collection();
        }

        return Building::where('user_id', $user->getKey())->get();
    }

    /**
     * A coach reaches zero or more buildings through building_coach_statuses. The extra conditions
     * mirror BuildingPolicy::accessBuilding(): the coach had to pass that gate to open the tool for
     * this building, and so to reach the SmartTwin handoff at all. Access revoked since means we do
     * not take the results in — otherwise any coach could finish an advice in SmartTwin for an
     * address they are not attached to and have us store it.
     *
     * @return Collection<int, Building>
     */
    private function coachBuildings(User $user): Collection
    {
        if (! $user->hasRole(RoleHelper::ROLE_COACH)) {
            return new Collection();
        }

        $buildingIds = BuildingCoachStatusService::getConnectedBuildingsByUser($user)->pluck('building_id');

        if ($buildingIds->isEmpty()) {
            return new Collection();
        }

        return Building::whereIn('id', $buildingIds)
            ->whereHas(
                'buildingPermissions',
                fn (Builder $query) => $query->where('user_id', $user->getKey()),
            )
            // forAllCooperations() for the same reason as above: this traverses to User, which
            // carries the CooperationScope.
            ->whereHas(
                'user',
                fn (Builder $query) => $query->forAllCooperations()->where('allow_access', true),
            )
            ->get();
    }

    /**
     * @param  Collection<int, Building>  $candidates
     * @param  array<string, mixed>  $data
     */
    private function matchOnAddress(
        Collection $candidates,
        array $data,
        Account $account,
        EventType $eventType,
    ): ?Building
    {
        $postalCode = $this->normalizePostalCode($data['PostalCode'] ?? null);
        $number     = $this->normalizeNumber($data['HouseNumber'] ?? null);

        if ($postalCode === '' || is_null($number)) {
            Log::warning('SmartTwin webhook: callback carries no usable address', [
                'account_id' => $account->getKey(),
                'flow'       => $eventType->value,
                'address'    => $this->addressContext($data),
            ]);

            return null;
        }

        $onNumber = $candidates->filter(
            fn (Building $building) => $this->normalizePostalCode($building->postal_code) === $postalCode
                && $this->normalizeNumber($building->number) === $number,
        );

        // We send one `extension` field and get it back split over a house letter and an addition,
        // with no guarantee it comes back the way we sent it. Glue them back together before
        // comparing, and compare on letters and digits only so "A-3", "a3" and "A 3" are one thing.
        $extension = $this->normalizeExtension(
            ($data['HouseLetter'] ?? '') . ($data['HouseNumberAddition'] ?? ''),
        );

        $matches = $onNumber->filter(
            fn (Building $building) => $this->normalizeExtension($building->extension) === $extension,
        );

        if ($matches->isEmpty() && $onNumber->count() === 1) {
            // Nothing left to confuse it with, so the addition is not what decides it here. Taken,
            // but logged: this is the one place where we accept a less than exact address.
            Log::warning('SmartTwin webhook: matched on postal code and house number only', [
                'account_id'  => $account->getKey(),
                'building_id' => $onNumber->first()->getKey(),
                'flow'        => $eventType->value,
                'address'     => $this->addressContext($data),
            ]);

            $matches = $onNumber;
        }

        if ($matches->count() === 1) {
            return $matches->first();
        }

        // Zero: the address is not one this account may reach. More than one: cooperations run their
        // own postcode areas so this should not happen, and if it does we would be guessing which
        // dossier to write into. Both end here.
        Log::warning('SmartTwin webhook: no single building matched the address', [
            'account_id'      => $account->getKey(),
            'flow'            => $eventType->value,
            'address'         => $this->addressContext($data),
            'candidate_ids'   => $candidates->pluck('id')->all(),
            'matched_ids'     => $matches->pluck('id')->all(),
        ]);

        return null;
    }

    private function normalizePostalCode(?string $postalCode): string
    {
        return (string) $this->addressService->normalizeZipcode($postalCode);
    }

    /**
     * buildings.number is free text, so it can hold "12" as well as "12 bis". Take the digits and
     * let the extension carry the rest.
     */
    private function normalizeNumber(int|string|null $number): ?int
    {
        if (preg_match('/\d+/', (string) $number, $matches) !== 1) {
            return null;
        }

        return (int) $matches[0];
    }

    private function normalizeExtension(?string $extension): string
    {
        return strtoupper((string) preg_replace('/[^a-z0-9]/i', '', (string) $extension));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function addressContext(array $data): array
    {
        return [
            'PostalCode'          => $data['PostalCode'] ?? null,
            'HouseNumber'         => $data['HouseNumber'] ?? null,
            'HouseLetter'         => $data['HouseLetter'] ?? null,
            'HouseNumberAddition' => $data['HouseNumberAddition'] ?? null,
        ];
    }
}
