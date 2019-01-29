<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class UserActionPlanAdviceComments extends Model
{
    use GetValueTrait;

    protected $fillable = ['user_id', 'input_source_id', 'comment'];

    /**
     * Normally we would use the GetMyValuesTrait, but that uses the building_id to query on.
     * The UserEnergyHabit uses the user_id instead off the building_id.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForMe($query)
    {
        $building = Building::find(HoomdossierSession::getBuilding());

        return $query->withoutGlobalScope(GetValueScope::class)->where('user_id', $building->user_id);
    }

    /**
     * Get the input Sources.
     *
     * @return InputSource
     */
    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }

    /**
     * Get a input source name.
     *
     * @return InputSource name
     */
    public function getInputSourceName()
    {
        return $this->inputSource()->first()->name;
    }
}
