<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationSetting
 *
 * @property int $id
 * @property int $user_id
 * @property int $type_id
 * @property int $interval_id
 * @property \Illuminate\Support\Carbon|null $last_notified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\NotificationInterval $interval
 * @property-read \App\Models\NotificationType $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereIntervalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereLastNotifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUserId($value)
 * @mixin \Eloquent
 */
class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id', 'type_id', 'interval_id', 'last_notified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_notified_at' => 'datetime',
        ];
    }

    /**
     * Get the interval of the notification.
     */
    public function interval(): BelongsTo
    {
        return $this->belongsTo(NotificationInterval::class);
    }

    /**
     * Get the type of notification.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }
}
