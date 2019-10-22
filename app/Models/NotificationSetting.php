<?php

namespace App;

use App\Models\NotificationInterval;
use App\Models\NotificationType;
use Illuminate\Database\Eloquent\Model;

/**
 * App\NotificationSetting.
 *
 * @property int                              $id
 * @property int                              $user_id
 * @property int                              $type_id
 * @property int                              $interval_id
 * @property \Illuminate\Support\Carbon|null  $last_notified_at
 * @property \Illuminate\Support\Carbon|null  $created_at
 * @property \Illuminate\Support\Carbon|null  $updated_at
 * @property \App\Models\NotificationInterval $interval
 * @property \App\Models\NotificationType     $type
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereIntervalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereLastNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\NotificationSetting whereUserId($value)
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id', 'type_id', 'interval_id', 'last_notified_at',
    ];
    /**
     * Attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'last_notified_at' => 'datetime',
    ];

    /**
     * Get the interval of the notification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function interval()
    {
        return $this->belongsTo(NotificationInterval::class);
    }

    /**
     * Get the type of notification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
