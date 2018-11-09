<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingCoachStatus extends Model
{
    protected $fillable = [
        'coach_id', 'status', 'building_id', 'appointment_date'
    ];

    protected $dates = [
        'appointment_date'
    ];

    // TODO: remove const when is removed in controllers etc
    const STATUS_IN_CONSIDERATION = "in_consideration";

    // status that will be used after a coordinator connected a coach to a resident or the "default" status.
    const STATUS_ACTIVE = "active";
    // status that will be used after a coach created a appointment with a resident.
    const STATUS_APPOINTMENT = "appointment";
    // status that will be used after the appointment is completed.
    const STATUS_DONE = "done";

    /**
     * Get the building from the status
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo('App\Models\Building');
    }

    /**
     * Get the coach from the status
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Returns the current status from a coach bases on the given status
     *
     * @param $status
     * @return Model|null|object|static
     */
    public function scopeCurrentStatus($query, $status)
    {

        return $query->where('status', $status)->where('coach_id', \Auth::id());
    }

    /**
     * Returns the translated status.
     *
     * @return string
     */
    public static function getCurrentStatusName($buildingId): string
    {
        $currentStatus = self::where('coach_id', \Auth::id())->where('building_id', $buildingId)->get()->last();

        if ($currentStatus instanceof BuildingCoachStatus) {
            return __('woningdossier.cooperation.admin.coach.buildings.index.table.options.'.$currentStatus->status);
        }
        return "";
    }

    /**
     * Returns the key of the status
     *
     * @return string
     */
    public static function getCurrentStatusKey($buildingId): string
    {
        $currentStatus = self::where('coach_id', \Auth::id())->where('building_id', $buildingId)->get()->last();

        if ($currentStatus instanceof BuildingCoachStatus) {
            return $currentStatus->status;
        }
        return "";
    }

}
