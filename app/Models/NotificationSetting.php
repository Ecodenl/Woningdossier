<?php

namespace App;

use App\Models\NotificationInterval;
use App\Models\NotificationType;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{

    protected $fillable = [
        'user_id', 'type_id', 'interval_id', 'last_notified_at'
    ];
    /**
     * Attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'last_notified_at' => 'datetime'
    ];

    /**
     * Get the interval of the notification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function interval()
    {
        return $this->belongsTo(NotificationInterval::class);
    }

    /**
     * Get the type of notification
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
