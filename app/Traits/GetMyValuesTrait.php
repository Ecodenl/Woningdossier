<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;

trait GetMyValuesTrait {

    public function scopeForMe($query)
    {
        return $query->where('building_id', HoomdossierSession::getBuilding());
    }

    public function inputSource()
    {
        return $this->belongsTo('App\Models\InputSource');
    }
}