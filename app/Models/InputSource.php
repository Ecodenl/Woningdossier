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
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InputSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InputSource extends Model
{
    use HasFactory;

    use HasShortTrait;

    const RESIDENT_SHORT = 'resident';
    const COACH_SHORT = 'coach';
    const COOPERATION_SHORT = 'cooperation';
    const MASTER_SHORT = 'master';
    const EXAMPLE_BUILDING = 'example-building';

    /**
     * Check if the input source is a resident.
     */
    public function isResident(): bool
    {
        return self::RESIDENT_SHORT == $this->short;
    }
}
