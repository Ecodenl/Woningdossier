<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelOrientation
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @property-read mixed $translations
 * @method static \Database\Factories\PvPanelOrientationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PvPanelOrientation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PvPanelOrientation extends Model
{
    use HasFactory,
        HasTranslations;

    protected $translatable = [
        'name',
    ];
}
