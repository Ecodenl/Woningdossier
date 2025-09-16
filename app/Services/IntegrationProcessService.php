<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\IntegrationProcess;
use App\Traits\Services\HasBuilding;
use Carbon\Carbon;

class IntegrationProcessService
{
    use HasBuilding;

    public Integration $integration;
    public string $process;

    public function forIntegration(Integration $integration): self
    {
        $this->integration = $integration;
        return $this;
    }

    public function forProcess(string $process): self
    {
        $this->process = $process;
        return $this;
    }

    public function resolve(): ?IntegrationProcess
    {
        return IntegrationProcess::where('integration_id', $this->integration->id)
            ->where('building_id', $this->building->id)
            ->where('process', $this->process)
            ->first();
    }

    public function lastSyncedAt(): ?Carbon
    {
        return $this->resolve()?->synced_at;
    }

    public function syncedNow(): void
    {
        IntegrationProcess::updateOrCreate([
            'integration_id' => $this->integration->id,
            'building_id' => $this->building->id,
            'process' => $this->process,
        ], ['synced_at' => Carbon::now()]);
    }
}
