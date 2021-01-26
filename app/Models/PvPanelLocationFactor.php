<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelLocationFactor.
 *
 * @property int                             $id
 * @property int                             $pc2
 * @property string                          $location
 * @property string                          $factor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor query()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor whereFactor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor wherePc2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelLocationFactor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PvPanelLocationFactor extends Model
{
}
