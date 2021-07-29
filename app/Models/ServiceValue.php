<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ServiceValue
 *
 * @property int $id
 * @property int|null $service_id
 * @property string $value
 * @property int|null $calculate_value
 * @property int $order
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\KeyFigureBoilerEfficiency|null $keyFigureBoilerEfficiency
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceValue whereValue($value)
 * @mixin \Eloquent
 */
class ServiceValue extends Model
{
    use HasTranslations;

    protected $translatable = [
        'value',
    ];

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
