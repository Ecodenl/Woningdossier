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
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolQuestionType whereUpdatedAt($value)
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

    protected $casts = [
        'name' => 'array'
    ];
}
