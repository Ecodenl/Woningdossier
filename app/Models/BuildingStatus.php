<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingStatus extends Model
{
    protected $casts = [
        'appointment_date' => 'datetime'
    ];

    public function scopeMostRecent($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function hasAppointmentDate(): bool
    {
        return $this->appointment_date instanceof \DateTime;
    }
}
