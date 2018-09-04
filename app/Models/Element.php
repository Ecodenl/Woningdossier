<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Element
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property int $service_type_id
 * @property int $order
 * @property string $info
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\ServiceType $serviceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ElementValue[] $values
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Element whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Element extends Model
{
    use TranslatableTrait;

    public function serviceType(){
    	return $this->belongsTo(ServiceType::class);
    }

    public function values(){
    	return $this->hasMany(ElementValue::class);
    }
}
