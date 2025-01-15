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
 * @property array<array-key, mixed> $name
 * @property array<array-key, mixed> $slug
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CompletedStep> $completedSteps
 * @property-read int|null $completed_steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Step> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubStep> $subSteps
 * @property-read int|null $sub_steps_count
 * @property-read mixed $translations
 * @method static Builder<static>|Scan bySlug(string $slug, string $locale = 'nl')
 * @method static Builder<static>|Scan expertScans()
 * @method static Builder<static>|Scan newModelQuery()
 * @method static Builder<static>|Scan newQuery()
 * @method static Builder<static>|Scan query()
 * @method static Builder<static>|Scan simpleScans()
 * @method static Builder<static>|Scan whereCreatedAt($value)
 * @method static Builder<static>|Scan whereId($value)
 * @method static Builder<static>|Scan whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Scan whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|Scan whereLocale(string $column, string $locale)
 * @method static Builder<static>|Scan whereLocales(string $column, array $locales)
 * @method static Builder<static>|Scan whereName($value)
 * @method static Builder<static>|Scan whereShort($value)
 * @method static Builder<static>|Scan whereSlug($value)
 * @method static Builder<static>|Scan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Scan extends Model
{
    use HasTranslations, HasShortTrait;

    const string LITE = 'lite-scan';
    const string QUICK = 'quick-scan';
    const string EXPERT = 'expert-scan';

    protected $translatable = ['name', 'slug'];

    // Static calls
    public static function lite(): ?self
    {
        return static::findByShort(self::LITE);
    }

    public static function quick(): ?self
    {
        return self::findByShort(self::QUICK);
    }

    public static function expert(): ?self
    {
        return self::findByShort(self::EXPERT);
    }

    public static function allShorts(): array
    {
        return [
            self::EXPERT,
            self::QUICK,
            self::LITE,
        ];
    }

    public static function simpleShorts(): array
    {
        return [
            self::QUICK,
            self::LITE,
        ];
    }

    public static function expertShorts(): array
    {
        return [
            self::EXPERT,
        ];
    }

    // Model Methods
    public function getRouteKeyName(): string
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
    public function scopeSimpleScans(Builder $query)
    {
        return $query->whereIn('short', [static::LITE, static::QUICK]);
    }

    public function scopeExpertScans(Builder $query)
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
