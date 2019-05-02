<?php

namespace App;

use App\Models\NotificationInterval;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{

    protected $casts = [
        'last_notified_at' => 'datetime'
    ];

    public function interval()
    {
        return $this->belongsTo(NotificationInterval::class);
    }
}
