<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelLocationFactor.
 *
 * @property int $id
 * @property int $pc2
 * @property string $location
 * @property float $factor
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor whereFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor wherePc2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelLocationFactor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PvPanelLocationFactor extends Model
{
}
