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
     */
    public static function translationMap(): array
    {
        return [
            Scan::QUICK => 'Uitgebreide variant',
            Scan::LITE => 'Eenvoudige variant',
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
        if ($types->diff([Scan::QUICK, Scan::EXPERT])->isEmpty()) {
            return Scan::QUICK;
        }

        // no other option m8.
        return Scan::LITE;
    }

    public function syncScan(string $type): void
    {
        $scansToSync = [
            Scan::QUICK => [Scan::QUICK, Scan::EXPERT],
            Scan::LITE => [Scan::LITE],
            'both-scans' => [Scan::LITE, Scan::QUICK, Scan::EXPERT],
        ];

        $scans = Scan::whereIn('short', $scansToSync[$type])->get();
        $this->cooperation->scans()->sync($scans);
    }
}
