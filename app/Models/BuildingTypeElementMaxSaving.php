<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BuildingTypeElementMaxSaving
 *
 * @property int $id
 * @property int $building_type_id
 * @property int $element_id
 * @property int $max_saving
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BuildingType $buildingType
 * @property-read \App\Models\Element $element
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereMaxSaving($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingTypeElementMaxSaving whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingTypeElementMaxSaving extends Model
{
    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }
}
