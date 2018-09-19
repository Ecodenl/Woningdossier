<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ElementValue.
 *
 * @property int $id
 * @property int $element_id
 * @property string $value
 * @property float|null $calculate_value
 * @property int $order
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\Element $element
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ElementValue whereValue($value)
 * @mixin \Eloquent
 */
class ElementValue extends Model
{
    use TranslatableTrait;

    public function element()
    {
        return $this->belongsTo(Element::class);
    }
}
