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
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssessmentType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AssessmentType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];
}
