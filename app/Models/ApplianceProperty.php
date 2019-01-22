<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApplianceProperty.
 *
 * @property int $id
 * @property int|null $appliance_id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\Appliance|null $appliance
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplianceProperty whereValue($value)
 * @mixin \Eloquent
 */
class ApplianceProperty extends Model
{
    public function appliance()
    {
        return $this->belongsTo(Appliance::class);
    }
}
