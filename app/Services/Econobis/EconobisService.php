<?php

namespace App\Services\Econobis;

use App\Models\Building;

class EconobisService
{
    private array $accountRelated;
    public ?Building $building = null;

    public function setAccountRelated(array $accountRelated): self
    {
        $this->accountRelated = $accountRelated;
        return $this;
    }

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function resolveAccountRelated(): array
    {
        if ($this->building instanceof Building) {
            $building = $this->building;
            return [
                'account_related' => [
                    'building_id' => $building->id,
                    'user_id' => $building->user->id,
                    'account_id' => $building->user->account_id,
                    'contact_id' => $building->user->extra['contact_id'] ?? null,
                ],
            ];
        }
        return ['account_related' => $this->accountRelated];
    }

    public function getPayload(?string $payloadClass = null): array
    {
        $defaultPayload = $this->resolveAccountRelated();
        $payload = [];
        // sometimes there are edge cases, those will be solved in a different manner.
        if ( ! is_null($payloadClass) && class_exists($payloadClass, true) && $this->building instanceof Building) {
            $payload = app($payloadClass)->forBuilding($this->building)->buildPayload();
        }
        return array_merge($defaultPayload, $payload);
    }
}