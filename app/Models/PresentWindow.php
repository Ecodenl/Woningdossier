<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PresentWindow
 *
 * @property int $id
 * @property string $name
 * @property int $calculate_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow query()
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PresentWindow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PresentWindow extends Model
{
}
