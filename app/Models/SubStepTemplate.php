<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SubStepTemplate
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property string $view
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubStepTemplate whereView($value)
 * @mixin \Eloquent
 */
class SubStepTemplate extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
    ];
}
