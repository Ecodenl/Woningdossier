<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaintworkStatus
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaintworkStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaintworkStatus extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
