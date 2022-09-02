<?php

namespace App\Services\Scans\ExpertScan;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Step;
use Illuminate\Http\Request;

class ScanableStep
{
    public Request $request;
    public Building $building;
    public InputSource $inputSource;
    public InputSource $masterInputSource;
    public Step $step;

    public function __construct(Step $step, Building $building, InputSource $inputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $this->step = $step;
    }

    public function request($request)
    {
        $this->request = $request;
    }
}