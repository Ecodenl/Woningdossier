<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserMotivation
 *
 * @property int $id
 * @property int $user_id
 * @property int $motivation_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Motivation $motivation
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereMotivationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserMotivation whereUserId($value)
 * @mixin \Eloquent
 */
class UserMotivation extends Model
{
    protected $fillable = ['user_id', 'motivation_id', 'order'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function motivation(): BelongsTo
    {
        return $this->belongsTo(Motivation::class);
    }
}
