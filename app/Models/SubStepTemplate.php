<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubStepTemplate
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property string $view
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SubStepTemplate whereView($value)
 * @mixin \Eloquent
 */
class SubStepTemplate extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
    ];
}
