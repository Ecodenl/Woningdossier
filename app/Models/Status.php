<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasOrder;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Status
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Status whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Status extends Model
{
    use HasTranslations,
        HasShortTrait,
        HasOrder;

    protected $translatable = [
        'name',
    ];
}
