<?php

namespace App\Traits;

use App\Helpers\HoomdossierSession;
use App\Models\InputSource;
use App\Scopes\GetValueScope;
use Illuminate\Support\Collection;

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
     * Check on a collection that comes from the forMe() scope if it contains a
     * Coach input source.
     *
     * @param Collection $inputSourcesForMe
     * @return bool
     */
    public static function hasCoachInputSource(Collection $inputSourcesForMe): bool
    {
        $coachInputSource = InputSource::findByShort('coach');

        if ($inputSourcesForMe->contains('input_source_id', $coachInputSource->id)) {
            return true;
        }

        return false;
    }

    /**
     * Check on a collection that comes from the forMe() scope if it contains a
     * Resident input source.
     *
     * @param Collection $inputSourcesForMe
     * @return bool
     */
    public static function hasResidentInputSource(Collection $inputSourcesForMe): bool
    {
        $residentInputSource = InputSource::findByShort('resident');

        if ($inputSourcesForMe->contains('input_source_id', $residentInputSource->id)) {
            return true;
        }

        return false;
    }

    /**
     * Get the coach input from a collection that comes from the forMe() scope
     *
     * @param Collection $inputSourcesForMe
     * @return mixed
     */
    public static function getCoachInput(Collection $inputSourcesForMe)
    {
        $coachInputSource = InputSource::findByShort('coach');

        if (self::hasCoachInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id', $coachInputSource->id)->first();
        }

    }
    
    /**
     * Get the resident input from a collection that comes from the forMe() scope
     *
     * @param Collection $inputSourcesForMe
     * @return mixed
     */
    public static function getResidentInput(Collection $inputSourcesForMe)
    {
        $residentInputSource = InputSource::findByShort('resident');

        if (self::hasResidentInputSource($inputSourcesForMe)) {
            return $inputSourcesForMe->where('input_source_id', $residentInputSource->id)->first();
        }

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