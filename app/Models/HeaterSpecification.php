<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\HeaterSpecification.
 *
 * @property int $id
 * @property int $liters
 * @property int $savings
 * @property int $boiler
 * @property float $collector
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereBoiler($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereCollector($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereLiters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereSavings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\HeaterSpecification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HeaterSpecification extends Model
{
}
