<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceValue
 *
 * @property int $id
 * @property int|null $service_id
 * @property array<array-key, mixed> $value
 * @property int|null $calculate_value
 * @property int $order
 * @property bool $is_default
 * @property array<array-key, mixed>|null $configurations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KeyFigureBoilerEfficiency|null $keyFigureBoilerEfficiency
 * @property-read \App\Models\Service|null $service
 * @property-read mixed $translations
 * @method static Builder<static>|ServiceValue byValue(string $name, string $locale = 'nl')
 * @method static Builder<static>|ServiceValue newModelQuery()
 * @method static Builder<static>|ServiceValue newQuery()
 * @method static Builder<static>|ServiceValue query()
 * @method static Builder<static>|ServiceValue whereCalculateValue($value)
 * @method static Builder<static>|ServiceValue whereConfigurations($value)
 * @method static Builder<static>|ServiceValue whereCreatedAt($value)
 * @method static Builder<static>|ServiceValue whereId($value)
 * @method static Builder<static>|ServiceValue whereIsDefault($value)
 * @method static Builder<static>|ServiceValue whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ServiceValue whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ServiceValue whereLocale(string $column, string $locale)
 * @method static Builder<static>|ServiceValue whereLocales(string $column, array $locales)
 * @method static Builder<static>|ServiceValue whereOrder($value)
 * @method static Builder<static>|ServiceValue whereServiceId($value)
 * @method static Builder<static>|ServiceValue whereUpdatedAt($value)
 * @method static Builder<static>|ServiceValue whereValue($value)
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
    #[Scope]
    protected function byValue(Builder $query, string $name, string $locale = 'nl'): Builder
    {
        return $query->where("value->{$locale}", $name);
    }

    # Relations
    public function keyFigureBoilerEfficiency(): HasOne
    {
        return $this->hasOne(KeyFigureBoilerEfficiency::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
