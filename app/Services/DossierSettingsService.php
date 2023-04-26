<?php

namespace App\Services;

use App\Models\DossierSetting;
use App\Models\InputSource;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DossierSettingsService
{
    use HasBuilding, HasInputSources;

    public string $type;

    public function forType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function justDone(): void
    {
        $where = [
            'building_id' => $this->building->id,
            'type' => $this->type,
        ];
        if ($this->inputSource instanceof InputSource) {
            $where['input_source_id'] = $this->inputSource->id;
        }

        DossierSetting::withoutGlobalScopes()
            ->updateOrCreate($where, ['done_at' => Carbon::now()]);
    }

    public function lastDoneAfter(Carbon $datetime)
    {
        $dossierSetting = DossierSetting::withoutGlobalScopes()
            ->forInputSource($this->inputSource)
            ->forBuilding($this->building)
            ->where('type', $this->type)
            ->first();

        Log::debug("Checking for reset payloadId reset done at: ".$dossierSetting->done_at);

        Log::debug("befor? ".$datetime->isBefore($dossierSetting->done_at));
        if ($datetime->isBefore($dossierSetting->done_at)) {
            return true;
        }
        return false;
    }
}

