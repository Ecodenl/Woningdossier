<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingStatus.
 *
 * @property int                             $id
 * @property int                             $building_id
 * @property int                             $status_id
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Status              $status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus mostRecent()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingStatus extends Model
{
    protected $fillable = [
        'status_id', 'building_id', 'appointment_date',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    public function scopeMostRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function hasAppointmentDate(): bool
    {
        return $this->appointment_date instanceof \DateTime;
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
