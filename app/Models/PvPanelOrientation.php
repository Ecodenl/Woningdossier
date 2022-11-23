<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PvPanelOrientation
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Database\Factories\PvPanelOrientationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PvPanelOrientation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PvPanelOrientation extends Model
{
    use HasFactory;

    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
