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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\NotificationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationType extends Model
{
    use TranslatableTrait;
}
