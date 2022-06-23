<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationInterval
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationInterval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationInterval extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
