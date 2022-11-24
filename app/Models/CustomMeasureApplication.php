<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Scopes\VisibleScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

/**
 * App\Models\CustomMeasureApplication
 *
 * @property int $id
 * @property array $name
 * @property array $info
 * @property string $hash
 * @property int $building_id
 * @property int $input_source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserActionPlanAdvice[] $userActionPlanAdvices
 * @property-read int|null $user_action_plan_advices_count
 * @method static Builder|CustomMeasureApplication allInputSources()
 * @method static \Database\Factories\CustomMeasureApplicationFactory factory(...$parameters)
 * @method static Builder|CustomMeasureApplication forBuilding($building)
 * @method static Builder|CustomMeasureApplication forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder|CustomMeasureApplication forMe(?\App\Models\User $user = null)
 * @method static Builder|CustomMeasureApplication forUser($user)
 * @method static Builder|CustomMeasureApplication newModelQuery()
 * @method static Builder|CustomMeasureApplication newQuery()
 * @method static Builder|CustomMeasureApplication query()
 * @method static Builder|CustomMeasureApplication residentInput()
 * @method static Builder|CustomMeasureApplication whereBuildingId($value)
 * @method static Builder|CustomMeasureApplication whereCreatedAt($value)
 * @method static Builder|CustomMeasureApplication whereHash($value)
 * @method static Builder|CustomMeasureApplication whereId($value)
 * @method static Builder|CustomMeasureApplication whereInfo($value)
 * @method static Builder|CustomMeasureApplication whereInputSourceId($value)
 * @method static Builder|CustomMeasureApplication whereName($value)
 * @method static Builder|CustomMeasureApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomMeasureApplication extends Model
{
    use HasFactory;

    use HasTranslations,
        GetMyValuesTrait,
        GetValueTrait;

    public $translatable = [
        'name', 'info',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'name', 'hash', 'info',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public function userActionPlanAdvices()
    {
        // We need to retrieve this without the visible tag
        // The visible tag defines whether it should be shown on my plan or not, but for other locations
        // (e.g. the question that adds them) it just defines if it's checked or not
        return $this->morphMany(
            UserActionPlanAdvice::class,
            'user_action_plan_advisable'
        )->withoutGlobalScope(VisibleScope::class);
    }

    public function getSibling(InputSource $inputSource)
    {
        return static::allInputSources()->where('hash', '=', $this->hash)->forInputSource(
            $inputSource
        )->first();
    }
}
