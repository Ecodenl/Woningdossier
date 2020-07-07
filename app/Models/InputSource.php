<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\InputSource.
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $short
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\InputSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class InputSource extends Model
{
    use HasShortTrait;

    const RESIDENT_SHORT = 'resident';
    const COACH_SHORT = 'coach';
    const COOPERATION_SHORT = 'cooperation';
    const EXAMPLE_BUILDING = 'example-building';

    /**
     * Check if the input source is a resident.
     *
     * @return bool
     */
    public function isResident(): bool
    {
        return self::RESIDENT_SHORT == $this->short;
    }
}
