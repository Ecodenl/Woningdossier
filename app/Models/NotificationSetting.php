<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationSetting.
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
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereIntervalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereLastNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereUserId($value)
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
