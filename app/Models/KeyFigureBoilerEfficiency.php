<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\KeyFigureBoilerEfficiency
 *
 * @property int $id
 * @property int $service_value_id
 * @property int $heating
 * @property int $wtw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ServiceValue $serviceValue
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereHeating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereServiceValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KeyFigureBoilerEfficiency whereWtw($value)
 * @mixin \Eloquent
 */
class KeyFigureBoilerEfficiency extends Model
{
    public function serviceValue(): BelongsTo
    {
        return $this->belongsTo(ServiceValue::class);
    }
}
