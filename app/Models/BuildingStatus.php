<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingStatus
 *
 * @property int $id
 * @property int $building_id
 * @property int $status_id
 * @property \Illuminate\Support\Carbon|null $appointment_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Status $status
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus mostRecent()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereAppointmentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingStatus whereUpdatedAt($value)
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
        // the higher the id the newer it is, ideally we would want to order on the created_at
        // but we cant rely on that because of migration from another table.
        return $query->orderByDesc('id');
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
