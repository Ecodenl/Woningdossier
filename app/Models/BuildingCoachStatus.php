<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCoachStatus.
 *
 * @property int $id
 * @property int $coach_id
 * @property int $building_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $private_message_id
 * @property \App\Models\Building $building
 * @property \App\Models\User $coach
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus currentStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCoachId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus wherePrivateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingCoachStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCoachStatus extends Model
{
    protected $fillable = [
        'coach_id', 'status', 'building_id', 'appointment_date', 'private_message_id',
    ];

    protected $dates = [
        'appointment_date',
    ];

    // TODO: remove const when is removed in controllers etc
    const STATUS_IN_CONSIDERATION = 'in_consideration';

    // status that will be used after a coordinator connected a coach to a resident, the coach is not able to set this himself
    const STATUS_ACTIVE = 'active';
    // status that will be used after a coach created a appointment with a resident.
    const STATUS_APPOINTMENT = 'appointment';
    // status that will be used after the appointment is moved
    const STATUS_NEW_APPOINTMENT = 'new_appointment';
    // status that will be used after the appointment is completed.
    const STATUS_DONE = 'done';

    const STATUS_REMOVED = 'removed';

    /**
     * Get the building from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function building()
    {
        return $this->belongsTo('App\Models\Building');
    }

    /**
     * Get the user from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'coach_id', 'id');
    }

    /**
     * Get the coach from the status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return $this->belongsTo('App\Models\User', 'coach_id', 'id');
    }

    /**
     * Returns the current status from a coach bases on the given status.
     *
     * @param $status
     *
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
        // should we show the last status that is attached or the status for the auth user / coach
//        $currentStatus = self::where('coach_id', \Auth::id())->where('building_id', $buildingId)->get()->last();
        $currentStatus = self::where('building_id', $buildingId)->get()->last();

        if ($currentStatus instanceof self) {
            return __('woningdossier.cooperation.admin.coach.buildings.index.table.options.'.$currentStatus->status);
        }

        return '';
    }

    /**
     * Returns the key of the status.
     *
     * @return string
     */
    public static function getCurrentStatusKey($buildingId): string
    {
        $currentStatus = self::where('coach_id', \Auth::id())->where('building_id', $buildingId)->get()->last();

        if ($currentStatus instanceof self) {
            return $currentStatus->status;
        }

        return '';
    }

    /**
     * A function to check if a coach has 'access' to a a building
     * if the active count i higher then the remove count he has 'access'
     * i say 'access' because he cant access the building without a building_permission, however he can access the building details and a groupchat.
     *
     * @param $buildingId
     * @param $coachId
     *
     * @return bool
     */
    public static function hasCoachAccess($buildingId, $coachId): bool
    {
        // count the active statuses
        $buildingCoachStatusActive = self::where('coach_id', '=', $coachId)
            ->where('building_id', $buildingId)
            ->where('status', '=', self::STATUS_ACTIVE)->count();

        // count the removed statuses
        $buildingCoachStatusRemoved = self::where('coach_id', '=', $coachId)
            ->where('building_id', $buildingId)
            ->where('status', '=', self::STATUS_REMOVED)->count();

        if ($buildingCoachStatusActive > $buildingCoachStatusRemoved) {
            return true;
        } else {
            return false;
        }
    }
}
