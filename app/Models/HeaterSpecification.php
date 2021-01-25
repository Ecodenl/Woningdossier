<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterSpecification
 *
 * @property int $id
 * @property int $liters
 * @property int $savings
 * @property int $boiler
 * @property string $collector
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification query()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereBoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereCollector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereLiters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereSavings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaterSpecification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterSpecification extends Model
{
}
