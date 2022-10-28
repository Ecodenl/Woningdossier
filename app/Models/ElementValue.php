<?php

namespace App\Models;

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
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereConfigurations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ElementValue whereValue($value)
 * @mixin \Eloquent
 */
class ElementValue extends Model
{
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
    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
