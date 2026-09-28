<?php

namespace App\Models;

use App\Traits\Models\HasOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\EnergyLabel
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BuildingFeature> $buildingFeatures
 * @property-read int|null $building_features_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EnergyLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EnergyLabel extends Model
{
    /**
     * The icon that shows this label as the coloured arrow people recognise. Seeded names are A
     * through G plus 'X' for none and '?' for unknown; anything else falls back to unknown rather
     * than pointing at an icon that does not exist.
     */
    public function iconClass(): string
    {
        return match (true) {
            $this->name === 'X' => 'icon-label-none',
            in_array($this->name, ['A', 'B', 'C', 'D', 'E', 'F', 'G']) => 'icon-label-' . strtolower($this->name),
            default => 'icon-label-unknown',
        };
    }

    use HasOrder;

    public function buildingFeatures(): HasMany
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
