<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationType
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationType extends Model
{
    use TranslatableTrait;

    const PRIVATE_MESSAGE = 'private-message';
}
