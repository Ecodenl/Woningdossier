<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceType
 *
 * @property int $id
 * @property array $name
 * @property string $iso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingService> $buildingServices
 * @property-read int|null $building_services_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Element> $elements
 * @property-read int|null $elements_count
 * @property-read mixed $translations
 * @method static \Database\Factories\ServiceTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereIso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ServiceType extends Model
{
    use HasFactory;

    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function elements(): HasMany
    {
        return $this->hasMany(Element::class);
    }

    public function buildingServices(): HasMany
    {
        // TODO: Broken
        return $this->hasMany(BuildingService::class);
    }
}
