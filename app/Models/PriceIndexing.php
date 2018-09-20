<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PriceIndexing.
 *
 * @property int $id
 * @property string $short
 * @property string $name
 * @property float $percentage
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PriceIndexing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceIndexing extends Model
{
    use TranslatableTrait;
}
