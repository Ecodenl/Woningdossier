<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Element
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property int $service_type_id
 * @property int $order
 * @property array $info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElementValue> $elementValues
 * @property-read int|null $element_values_count
 * @property-read array $translations
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ElementValue> $values
 * @property-read int|null $values_count
 * @method static \Database\Factories\ElementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element query()
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Element extends Model
{
    use HasFactory;

    use HasShortTrait,
        HasTranslations;

    protected $translatable = [
        'name', 'info',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values()
    {
        return $this->hasMany(ElementValue::class);
    }

    public function elementValues(): HasMany
    {
        return $this->hasMany(ElementValue::class);
    }
}
