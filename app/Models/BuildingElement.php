<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\BuildingElement
 *
 * @property int $id
 * @property int|null $building_id
 * @property int|null $input_source_id
 * @property int $element_id
 * @property int|null $element_value_id
 * @property array<array-key, mixed>|null $extra
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @property-read \App\Models\Element $element
 * @property-read \App\Models\ElementValue|null $elementValue
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement allInputSources()
 * @method static \Database\Factories\BuildingElementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement forBuilding(\App\Models\Building|int $building)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement forUser(\App\Models\User|int $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereElementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereElementValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BuildingElement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BuildingElement extends Model implements Auditable
{
    use HasFactory;

    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    protected $fillable = ['building_id', 'input_source_id', 'element_id', 'element_value_id', 'extra'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function element(): BelongsTo
    {
        return $this->belongsTo(Element::class);
    }

    public function elementValue(): BelongsTo
    {
        return $this->belongsTo(ElementValue::class);
    }
}
