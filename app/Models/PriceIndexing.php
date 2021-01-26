<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PriceIndexing.
 *
 * @property int                             $id
 * @property string                          $short
 * @property string                          $name
 * @property string                          $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing query()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceIndexing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceIndexing extends Model
{
    use TranslatableTrait;
}
