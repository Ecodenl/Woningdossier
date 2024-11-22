<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ElementValue
 *
 * @property int $id
 * @property int $element_id
 * @property array $value
 * @property float|null $calculate_value
 * @property int $order
 * @property array|null $configurations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Element $element
 * @property-read int $insulation_factor
 * @property-read mixed $translations
 * @method static \Database\Factories\ElementValueFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereConfigurations($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ElementValue whereValue($value)
 * @mixin \Eloquent
 */
class ElementValue extends Model
{
    use HasFactory;

    use HasTranslations;

    protected $translatable = [
        'value',
    ];

    protected $casts = [
        'configurations' => 'array',
    ];

    # Attributes
    public function getInsulationFactorAttribute(): int
    {
        $configurations = $this->configurations;
        return (int) ($configurations['insulation_factor'] ?? 0);
    }

    # Relations
    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }
}
