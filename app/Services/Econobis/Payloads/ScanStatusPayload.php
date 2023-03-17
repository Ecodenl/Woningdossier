<?php

namespace App\Services\Econobis\Payloads;

use App\Models\Scan;

class ScanStatusPayload extends EconobisPayload
{
    use MasterInputSource;

    public function buildPayload(): array
    {
        $cooperation = $this->building->user->cooperation;

        $scans = $cooperation->load(['scans' => fn($q) => $q->where('short', '!=', Scan::EXPERT)])->scans;
        $data['scans'] = [];
        foreach ($scans as $scan) {
            $data['scans'][$scan->short] = [
                'id' => $scan->id,
                'name' => $scan->name,
                'short' => $scan->short,
                'is_completed' => $this->building->hasCompletedScan($scan, $this->masterInputSource),
            ];
        }
        return $data;
    }
}