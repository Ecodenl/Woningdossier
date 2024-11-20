<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QuestionOption
 *
 * @property int $id
 * @property int $question_id
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionOption whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class QuestionOption extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    protected $fillable = [
        'question_id', 'name',
    ];
}
