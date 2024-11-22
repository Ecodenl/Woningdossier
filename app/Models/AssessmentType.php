<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AssessmentType
 *
 * @property int $id
 * @property string $type
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssessmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssessmentType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
