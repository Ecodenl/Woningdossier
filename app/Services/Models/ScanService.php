<?php

namespace App\Services\Models;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Scan;
use App\Traits\FluentCaller;
use Illuminate\Support\Facades\DB;

class ScanService
{
    use FluentCaller;

    protected Scan $scan;
    protected Building $building;
    protected InputSource $masterInputSource;

    public function __construct()
    {
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    public function scan(Scan $scan): self
    {
        $this->scan = $scan;
        return $this;
    }

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function hasMadeScanProgress(): bool
    {
        return DB::table('completed_sub_steps AS css')
            ->leftJoin('sub_steps', 'css.sub_step_id', '=', 'sub_steps.id')
            ->leftJoin('steps', 'sub_steps.step_id', '=', 'steps.id')
            ->where('steps.scan_id', $this->scan->id)
            ->where('css.building_id', $this->building->id)
            ->where('css.input_source_id', $this->masterInputSource->id)
            ->count() > 0;
    }
}