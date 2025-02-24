<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationType
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    const PRIVATE_MESSAGE = 'private-message';
}
