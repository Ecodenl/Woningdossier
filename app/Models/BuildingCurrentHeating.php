<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingCurrentHeating
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingCurrentHeating whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingCurrentHeating extends Model
{
}
