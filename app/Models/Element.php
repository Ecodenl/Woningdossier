<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read array $translations
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ElementValue[] $values
 * @property-read int|null $values_count
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
    use HasShortTrait,
        HasTranslations;

    protected $translatable = [
        'name', 'info',
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function values()
    {
        return $this->hasMany(ElementValue::class);
    }
}
