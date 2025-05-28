<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InputSource
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Database\Factories\InputSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InputSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InputSource extends Model
{
    use HasFactory;

    use HasShortTrait;

    const string RESIDENT_SHORT = 'resident';
    const string COACH_SHORT = 'coach';
    const string COOPERATION_SHORT = 'cooperation';
    const string MASTER_SHORT = 'master';
    const string EXAMPLE_BUILDING_SHORT = 'example-building';
    const string EXTERNAL_SHORT = 'external';

    /**
     * Check if the input source is a resident.
     */
    public function isResident(): bool
    {
        return self::RESIDENT_SHORT == $this->short;
    }

    public function isMaster(): bool
    {
        return self::MASTER_SHORT == $this->short;
    }

    public static function master(): ?self
    {
        return self::findByShort(static::MASTER_SHORT);
    }

    public static function coach(): ?self
    {
        return self::findByShort(static::COACH_SHORT);
    }

    public static function resident(): ?self
    {
        return self::findByShort(static::RESIDENT_SHORT);
    }

    public static function exampleBuilding(): ?self
    {
        return self::findByShort(static::EXAMPLE_BUILDING_SHORT);
    }

    public static function external(): ?self
    {
        return self::findByShort(static::EXTERNAL_SHORT);
    }
}
