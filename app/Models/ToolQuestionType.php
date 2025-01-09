<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ToolQuestionType
 *
 * @property int $id
 * @property array $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ToolQuestionType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolQuestionType extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'short'
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array'
        ];
    }
}
