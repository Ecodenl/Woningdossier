<?php

namespace App\Exports\Cooperation;

use Maatwebsite\Excel\Concerns\FromArray;

class TotalExport implements FromArray
{
    protected $totalExport;

    public function __construct(array $totalExport)
    {
        $this->totalExport = $totalExport;
    }

    public function array(): array
    {
        return $this->totalExport;
    }
}
