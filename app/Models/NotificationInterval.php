<?php

namespace App\Models;

use App\Scopes\OrderScope;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NotificationInterval
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationInterval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NotificationInterval extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OrderScope());
    }
}
