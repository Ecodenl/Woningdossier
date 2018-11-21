<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeating.
 *
 * @property int $id
 * @property string $name
 * @property int|null $degree
 * @property int|null $calculate_value
 * @property bool $is_default
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BuildingHeating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeating extends Model
{
    use TranslatableTrait;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
}
