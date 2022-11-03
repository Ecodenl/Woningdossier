<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ToolLabel
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolLabel extends Model
{
    use HasShortTrait, HasTranslations;

    protected $translatable = [
        'name',
        'slug',
    ];
}
