<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Element.
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
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\ElementValue[] $values
 * @property int|null                                                            $values_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Element query()
 * @method static \Illuminate\Database\Eloquent\Builder|Element translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereServiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Element whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Element extends Model
{
    use TranslatableTrait;
    use HasShortTrait;

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function values()
    {
        return $this->hasMany(ElementValue::class);
    }
}
