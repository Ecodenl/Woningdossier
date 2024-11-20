<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ventilation
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ventilation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ventilation extends Model
{
    const NATURAL = 'natural';
    const MECHANICAL = 'mechanical';
    const BALANCED = 'balanced';
    const DECENTRAL = 'decentral';

    /**
     * Get ventilation types mapped by calculate value of the relevant service_value
     *
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            1 => static::NATURAL,
            2 => static::MECHANICAL,
            3 => static::BALANCED,
            4 => static::DECENTRAL,
        ];
    }    
}
