<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Service.
 *
 * @property int                                                                 $id
 * @property string                                                              $name
 * @property string                                                              $short
 * @property int                                                                 $service_type_id
 * @property int                                                                 $order
 * @property string                                                              $info
 * @property \Illuminate\Support\Carbon|null                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                     $updated_at
 * @property \App\Models\ServiceType                                             $serviceType
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceValue[] $values
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Service whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Service extends Model
{
    use TranslatableTrait, HasShortTrait;

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function values()
    {
        return $this->hasMany(ServiceValue::class);
    }
}
