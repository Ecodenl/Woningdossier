<?php

namespace App\Services;

use App\Models\DossierSetting;
use App\Models\InputSource;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;
use Carbon\Carbon;

class DossierSettingsService
{
    use HasBuilding, HasInputSources;

    public string $type;

    public function forType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function justDone(string $type): void
    {
        $where = [
            'building_id' => $this->building->id,
        ];
        if ($this->inputSource instanceof InputSource) {
            $where['input_source_id'] = $this->inputSource->id;
        }
        DossierSetting::withoutGlobalScopes()
            ->updateOrCreate($where, ['done_at' => Carbon::now()]);
    }
}

