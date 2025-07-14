<?php

namespace App\Models;

use App\Observers\CustomMeasureApplicationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Models\HasMappings;
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
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed> $info
 * @property string $hash
 * @property int $building_id
 * @property int $input_source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @property-read mixed $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserActionPlanAdvice> $userActionPlanAdvices
 * @property-read int|null $user_action_plan_advices_count
 * @method static Builder<static>|CustomMeasureApplication allInputSources()
 * @method static \Database\Factories\CustomMeasureApplicationFactory factory($count = null, $state = [])
 * @method static Builder<static>|CustomMeasureApplication forBuilding(\App\Models\Building|int $building)
 * @method static Builder<static>|CustomMeasureApplication forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder<static>|CustomMeasureApplication forMe(?\App\Models\User $user = null)
 * @method static Builder<static>|CustomMeasureApplication forUser(\App\Models\User|int $user)
 * @method static Builder<static>|CustomMeasureApplication newModelQuery()
 * @method static Builder<static>|CustomMeasureApplication newQuery()
 * @method static Builder<static>|CustomMeasureApplication query()
 * @method static Builder<static>|CustomMeasureApplication residentInput()
 * @method static Builder<static>|CustomMeasureApplication whereBuildingId($value)
 * @method static Builder<static>|CustomMeasureApplication whereCreatedAt($value)
 * @method static Builder<static>|CustomMeasureApplication whereHash($value)
 * @method static Builder<static>|CustomMeasureApplication whereId($value)
 * @method static Builder<static>|CustomMeasureApplication whereInfo($value)
 * @method static Builder<static>|CustomMeasureApplication whereInputSourceId($value)
 * @method static Builder<static>|CustomMeasureApplication whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomMeasureApplication whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|CustomMeasureApplication whereLocale(string $column, string $locale)
 * @method static Builder<static>|CustomMeasureApplication whereLocales(string $column, array $locales)
 * @method static Builder<static>|CustomMeasureApplication whereName($value)
 * @method static Builder<static>|CustomMeasureApplication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy([CustomMeasureApplicationObserver::class])]
class CustomMeasureApplication extends Model
{
    use HasFactory;

    use HasTranslations,
        GetMyValuesTrait,
        GetValueTrait,
        HasMappings;

    public $translatable = [
        'name', 'info',
    ];

    protected $fillable = [
        'building_id', 'input_source_id', 'name', 'hash', 'info',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function userActionPlanAdvices(): MorphMany
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
