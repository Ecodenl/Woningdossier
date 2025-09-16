<?php

namespace App\Services\Econobis\Payloads;

use App\Models\Scan;
use App\Services\Models\ScanService;

class ScanStatusPayload extends EconobisPayload
{
    use MasterInputSource;

    public ScanService $scanService;

    public function __construct(ScanService $scanService)
    {
        $this->scanService = $scanService;
    }

    public function buildPayload(): array
    {
        $cooperation = $this->building->user->cooperation;

        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;
        $data['scans'] = [];
        foreach ($scans as $scan) {
            $hasMadeProgress = $this
                ->scanService
                ->forBuilding($this->building)
                ->scan($scan)
                ->hasMadeScanProgress();

            $data['scans'][$scan->short] = [
                'id' => $scan->id,
                'name' => $scan->name,
                'short' => $scan->short,
                'has_made_progress' => $hasMadeProgress,
            ];
        }
        return $data;
    }
}
