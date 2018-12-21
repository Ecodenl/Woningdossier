<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingElement.
 *
 * @property int $id
 * @property int|null $building_id
 * @property int $element_id
 * @property int|null $element_value_id
 * @property array $extra
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Building|null $building
 * @property \App\Models\Element $element
 * @property \App\Models\ElementValue|null $elementValue
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingElement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingElement extends Model
{
    use GetValueTrait;
    use GetMyValuesTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
    ];

    protected $fillable = ['building_id', 'input_source_id', 'element_id', 'element_value_id', 'extra'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function element()
    {
        return $this->belongsTo(Element::class);
    }

    public function elementValue()
    {
        return $this->belongsTo(ElementValue::class);
    }
}
