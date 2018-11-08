<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelOrientation.
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PvPanelOrientation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PvPanelOrientation extends Model
{
    use TranslatableTrait;
}
