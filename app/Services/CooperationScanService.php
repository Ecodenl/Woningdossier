<?php

namespace App\Services;

use App\Models\Cooperation;
use App\Models\Scan;
use App\Traits\FluentCaller;

class CooperationScanService
{
    use FluentCaller;

    public Cooperation $cooperation;

    public function __construct(Cooperation $cooperation)
    {
        $this->cooperation = $cooperation;
    }

    /**
     * A mapping from user translation to our scans.
     *
     * @return array
     */
    public static function translationMap(): array
    {
        return [
            'quick-scan' => 'Uitgebreide variant',
            'lite-scan' => 'Eenvoudige variant',
            'both-scans' => 'Beide varianten'
        ];
    }

    public function getCurrentType(): string
    {
        $scans = $this->cooperation->scans;
        $types = $scans->pluck('short');

        // only 1 type has 3 scans, which is both-scans.
        if ($scans->count() === 3) {
            return 'both-scans';
        }

        // when the current types have no diff, the collection is empty and we can safely assume its a quick-scan
        if ($types->diff(['quick-scan', 'expert-scan'])->isEmpty()) {
            return  'quick-scan';
        }

        // no other option m8.
        return  'lite-scan';
    }

    public function syncScan(string $type): void
    {
        $scansToSync = [
            'quick-scan' => ['quick-scan', 'expert-scan'],
            'lite-scan' =>  ['lite-scan'],
            'both-scans' => ['quick-scan', 'lite-scan', 'expert-scan'],
        ];

        $scans = Scan::whereIn('short', $scansToSync[$type])->get();
        $this->cooperation->scans()->sync($scans);
    }
}