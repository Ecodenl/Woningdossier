<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
 * @method static Builder|Scan expert()
 * @method static Builder|Scan newModelQuery()
 * @method static Builder|Scan newQuery()
 * @method static Builder|Scan query()
 * @method static Builder|Scan simple()
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

    const LITE = 'lite-scan';
    const QUICK = 'quick-scan';
    const EXPERT = 'expert-scan';

    protected $translatable = ['name', 'slug'];

    // Static calls
    public static function lite(): ?Model
    {
        return static::findByShort(self::LITE);
    }

    public static function quick(): ?Model
    {
        return self::findByShort(self::QUICK);
    }

    public static function expert(): ?Model
    {
        return self::findByShort(self::EXPERT);
    }

    // Model Methods
    public function getRouteKeyName()
    {
        $locale = App::getLocale();
        return "slug->{$locale}";
    }

    public function getRouteKey()
    {
        return $this->slug;
    }

    public function isLiteScan(): bool
    {
        return $this->short === self::LITE;
    }

    public function isQuickScan(): bool
    {
        return $this->short === self::QUICK;
    }

    public function isExpertScan(): bool
    {
        return $this->short === self::EXPERT;
    }

    // Scopes
    public function scopeSimple(Builder $query)
    {
        return $query->whereIn('short', [static::LITE, static::QUICK]);
    }

    public function scopeExpert(Builder $query)
    {
        return $query->whereIn('short', [static::EXPERT]);
    }

    // TODO: Slug trait?
    public function scopeBySlug(Builder $query, string $slug, string $locale = 'nl'): Builder
    {
        return $query->where("slug->{$locale}", $slug);
    }

    // Relations
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function subSteps(): HasManyThrough
    {
        return $this->hasManyThrough(SubStep::class, Step::class);
    }

    public function completedSteps(): HasManyThrough
    {
        return $this->hasManyThrough(CompletedStep::class, Step::class);
    }
}
