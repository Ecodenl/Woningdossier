<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureBoilerEfficiency.
 *
 * @property int                             $id
 * @property int                             $service_value_id
 * @property int                             $heating
 * @property int                             $wtw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \App\Models\ServiceValue        $serviceValue
 *
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency query()
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereHeating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KeyFigureBoilerEfficiency whereWtw($value)
 * @mixin \Eloquent
 */
class KeyFigureBoilerEfficiency extends Model
{
    public function serviceValue()
    {
        return $this->belongsTo(ServiceValue::class);
    }
}
