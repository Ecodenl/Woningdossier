<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Scan
 *
 * @property int $id
 * @property array $name
 * @property array $slug
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CompletedStep[] $completedSteps
 * @property-read int|null $completed_steps_count
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SubStep[] $subSteps
 * @property-read int|null $sub_steps_count
 * @method static Builder|Scan bySlug(string $slug, string $locale = 'nl')
 * @method static Builder|Scan newModelQuery()
 * @method static Builder|Scan newQuery()
 * @method static Builder|Scan query()
 * @method static Builder|Scan whereCreatedAt($value)
 * @method static Builder|Scan whereId($value)
 * @method static Builder|Scan whereName($value)
 * @method static Builder|Scan whereShort($value)
 * @method static Builder|Scan whereSlug($value)
 * @method static Builder|Scan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Scan extends Model
{
    use HasTranslations, HasShortTrait;

    const EXPERT = 'expert-scan';

    protected $translatable = ['name', 'slug'];

    public function getRouteKeyName()
    {
        $locale = App::getLocale();
        return "slug->{$locale}";
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function subSteps()
    {
        return $this->hasManyThrough(SubStep::class, Step::class);
    }

    public function completedSteps()
    {
        return $this->hasManyThrough(CompletedStep::class, Step::class);
    }

    // TODO: Slug trait?
    public function scopeBySlug(Builder $query, string $slug, string $locale = 'nl'): Builder
    {
        return $query->where("slug->{$locale}", $slug);
    }
}
