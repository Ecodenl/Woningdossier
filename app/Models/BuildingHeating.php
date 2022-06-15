<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeating
 *
 * @property int $id
 * @property array $name
 * @property int|null $degree
 * @property int|null $calculate_value
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeating extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
}
