<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\HoomdossierSession;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\ExampleBuilding
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property int|null $building_type_id
 * @property int|null $cooperation_id
 * @property int|null $order
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BuildingType|null $buildingType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExampleBuildingContent> $contents
 * @property-read int|null $contents_count
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read mixed $translations
 * @method static Builder<static>|ExampleBuilding forAnyOrMyCooperation()
 * @method static Builder<static>|ExampleBuilding forMyCooperation()
 * @method static Builder<static>|ExampleBuilding generic()
 * @method static Builder<static>|ExampleBuilding newModelQuery()
 * @method static Builder<static>|ExampleBuilding newQuery()
 * @method static Builder<static>|ExampleBuilding query()
 * @method static Builder<static>|ExampleBuilding whereBuildingTypeId($value)
 * @method static Builder<static>|ExampleBuilding whereCooperationId($value)
 * @method static Builder<static>|ExampleBuilding whereCreatedAt($value)
 * @method static Builder<static>|ExampleBuilding whereId($value)
 * @method static Builder<static>|ExampleBuilding whereIsDefault($value)
 * @method static Builder<static>|ExampleBuilding whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ExampleBuilding whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|ExampleBuilding whereLocale(string $column, string $locale)
 * @method static Builder<static>|ExampleBuilding whereLocales(string $column, array $locales)
 * @method static Builder<static>|ExampleBuilding whereName($value)
 * @method static Builder<static>|ExampleBuilding whereOrder($value)
 * @method static Builder<static>|ExampleBuilding whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExampleBuilding extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public $fillable = [
        'name', 'building_type_id', 'cooperation_id', 'order', 'is_default',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($model) {
            /* @var ExampleBuilding $model */
            // delete contents
            $model->contents()->delete();
            \Log::debug('Deleting done..');
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function contents(): HasMany
    {
        return $this->hasMany(ExampleBuildingContent::class);
    }

    public function buildingType(): BelongsTo
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    /**
     * @param $year
     */
    public function getContentForYear($year): ?ExampleBuildingContent
    {
        $content = $this->contents()
            ->where('build_year', '<=', $year)
            ->orderBy('build_year', 'desc')
            ->first();

        if ($content instanceof ExampleBuildingContent) {
            return $content;
        }

        return $this->contents()
            ->whereNull('build_year')
            ->first();
    }

    /**
     * Returns if this ExampleBuilding is a specific example building or not.
     */
    public function isSpecific(): bool
    {
        return ! is_null($this->cooperation_id);
    }

    /**
     * Returns if this ExampleBuilding is a generic example building or not.
     */
    public function isGeneric(): bool
    {
        return is_null($this->cooperation_id);
    }

    /**
     * Scope a query to only include buildings for my cooperation.
     */
    public function scopeForMyCooperation(Builder $query): Builder
    {
        $cooperationId = ! empty(HoomdossierSession::getCooperation()) ? HoomdossierSession::getCooperation() : 0;

        return $query->where('cooperation_id', '=', $cooperationId);
    }

    /**
     * Scope a query to only include buildings for my cooperation.
     */
    public function scopeForAnyOrMyCooperation(Builder $query): Builder
    {
        $cooperationId = \Session::get('cooperation', 0);

        return $query->where('cooperation_id', '=', $cooperationId)->orWhereNull('cooperation_id');
    }

    /**
     * Scope on only generic example buildings.
     */
    public function scopeGeneric(Builder $query): Builder
    {
        return $query->whereNull('cooperation_id');
    }
}
