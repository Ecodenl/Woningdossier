<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use App\Traits\ToolSettingTrait;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\BuildingElement
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int $element_id
 * @property int|null $element_value_id
 * @property array|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Element $element
 * @property-read \App\Models\ElementValue|null $elementValue
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingElement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingElement extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        ToolSettingTrait,
        \App\Traits\Models\Auditable;

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
