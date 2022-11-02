<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ToolLabel
 *
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolLabel query()
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
