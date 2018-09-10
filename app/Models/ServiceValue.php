<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceValue.
 *
 * @property int $id
 * @property int|null $service_id
 * @property string $value
 * @property int|null $calculate_value
 * @property int $order
 * @property bool $is_default
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \App\Models\KeyFigureBoilerEfficiency $keyFigureBoilerEfficiency
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ServiceValue whereValue($value)
 * @mixin \Eloquent
 */
class ServiceValue extends Model
{
    use TranslatableTrait;

    public function keyFigureBoilerEfficiency()
    {
        return $this->hasOne(KeyFigureBoilerEfficiency::class);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];
}
