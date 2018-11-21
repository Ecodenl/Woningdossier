<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Scopes\GetValueScope;

trait GetMyValuesTrait {

    /**
     * Scope all the available input for a user
     *
     * @param $query
     * @return mixed
     */
    public function scopeForMe($query)
    {
        return $query->withoutGlobalScope(GetValueScope::class)->where('building_id', HoomdossierSession::getBuilding());
    }

    /**
     * Get the input Sources
     *
     * @return InputSource
     */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }

    /**
     * Get a input source name
     *
     * @return InputSource name
     */
    public function getInputSourceName()
    {
        return $this->inputSource()->first()->name;
    }
//
//    /**
//     * Almost the same as getBuildingElement($short) except this returns all the input
//     *
//     * @param $query
//     * @param $short
//     * @return mixed
//     */
//    public function scopeBuildingElementsForMe($query, $short)
//    {
//        return $query->forMe()->leftJoin('elements as e', 'building_elements.element_id', '=', 'e.id')
//            ->where('e.short', $short)->select(['building_elements.*']);
//    }


}