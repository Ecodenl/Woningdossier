<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingHeatingApplication
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingHeatingApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingHeatingApplication extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
