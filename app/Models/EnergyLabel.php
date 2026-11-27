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
     * The icon that shows this label as the coloured arrow people recognise. Seeded names are A++++
     * through G plus 'X' for none and '?' for unknown. There are no icons above A, so A+ to A++++
     * show the one of A (see isAboveA()); anything else falls back to unknown rather than pointing
     * at an icon that does not exist.
     */
    public function iconClass(): string
    {
        return static::iconClassFor($this->name);
    }

    /**
     * iconClass() for a label known by its name only, such as one SmartTwin calculated.
     */
    public static function iconClassFor(?string $name): string
    {
        return match (true) {
            $name === 'X' => 'icon-label-none',
            static::isAboveA($name) => 'icon-label-a',
            in_array($name, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], true) => 'icon-label-' . strtolower($name),
            default => 'icon-label-unknown',
        };
    }

    /**
     * Whether this is one of the labels above A (A+ to A++++). They share the icon of A, so wherever
     * one is shown as an icon it needs its name next to it to be told apart from A.
     */
    public static function isAboveA(?string $name): bool
    {
        return in_array($name, ['A+', 'A++', 'A+++', 'A++++'], true);
    }

    /**
     * The label for a name as an external source gives it ("C", "a+++"), or 'X' (none) when it is
     * empty or not a label we know.
     */
    public static function fromName(?string $name): self
    {
        $name = strtoupper(trim((string) $name));

        return static::where('name', $name)->first()
            ?? static::where('name', 'X')->firstOrFail();
    }

    use HasOrder;

    public function buildingFeatures(): HasMany
    {
        return $this->hasMany(BuildingFeature::class);
    }
}
