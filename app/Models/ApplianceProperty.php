<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ApplianceProperty
 *
 * @property int $id
 * @property int|null $appliance_id
 * @property string $name
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Appliance|null $appliance
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
    public function appliance(){
    	return $this->belongsTo(Appliance::class);
    }
}
