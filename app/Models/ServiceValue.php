<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceValue
 *
 * @property int $id
 * @property int|null $service_id
 * @property array $value
 * @property int|null $calculate_value
 * @property int $order
 * @property bool $is_default
 * @property array|null $configurations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \App\Models\KeyFigureBoilerEfficiency|null $keyFigureBoilerEfficiency
 * @property-read \App\Models\Service|null $service
 * @method static Builder|ServiceValue byValue(string $name, string $locale = 'nl')
 * @method static Builder|ServiceValue newModelQuery()
 * @method static Builder|ServiceValue newQuery()
 * @method static Builder|ServiceValue query()
 * @method static Builder|ServiceValue whereCalculateValue($value)
 * @method static Builder|ServiceValue whereConfigurations($value)
 * @method static Builder|ServiceValue whereCreatedAt($value)
 * @method static Builder|ServiceValue whereId($value)
 * @method static Builder|ServiceValue whereIsDefault($value)
 * @method static Builder|ServiceValue whereOrder($value)
 * @method static Builder|ServiceValue whereServiceId($value)
 * @method static Builder|ServiceValue whereUpdatedAt($value)
 * @method static Builder|ServiceValue whereValue($value)
 * @mixin \Eloquent
 */
class ServiceValue extends Model
{
    use HasTranslations;

    protected $translatable = [
        'value',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'configurations' => 'array',
    ];

    # Scopes
    public function scopeByValue(Builder $query, string $name, string $locale = 'nl'): Builder
    {
        return $query->where("value->{$locale}", $name);
    }

    # Relations
    public function keyFigureBoilerEfficiency()
    {
        return $this->hasOne(KeyFigureBoilerEfficiency::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
