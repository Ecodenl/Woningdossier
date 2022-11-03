<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\ToolCalculationResult
 *
 * @property int $id
 * @property array $name
 * @property array|null $help_text
 * @property string $short
 * @property string $data_type
 * @property string|null $unit_of_measure
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubSteppable[] $subSteppables
 * @property-read int|null $sub_steppables_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubStep[] $subSteps
 * @property-read int|null $sub_steps_count
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereDataType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereHelpText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereUnitOfMeasure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToolCalculationResult whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ToolCalculationResult extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
        'help_text'
    ];

    # Relations
    public function subSteppables(): MorphMany
    {
        return $this->morphMany(SubSteppable::class, 'sub_steppable');
    }

    public function subSteps(): BelongsToMany
    {
        return $this->morphToMany(SubStep::class, 'sub_steppable')
            ->using(SubSteppable::class)
            ->withPivot('order', 'size', 'conditions', 'tool_question_type_id');
    }
}
