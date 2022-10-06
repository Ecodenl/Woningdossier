<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\ExampleBuilding
 *
 * @property int $id
 * @property array $name
 * @property int|null $building_type_id
 * @property int|null $cooperation_id
 * @property int|null $order
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BuildingType|null $buildingType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExampleBuildingContent[] $contents
 * @property-read int|null $contents_count
 * @property-read \App\Models\Cooperation|null $cooperation
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding forAnyOrMyCooperation()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding forMyCooperation()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding generic()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereBuildingTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExampleBuilding whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExampleBuilding extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    public $fillable = [
        'name', 'building_type_id', 'cooperation_id', 'order', 'is_default',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contents()
    {
        return $this->hasMany(ExampleBuildingContent::class);
    }

    public function buildingType()
    {
        return $this->belongsTo(BuildingType::class);
    }

    public function cooperation()
    {
        return $this->belongsTo(Cooperation::class);
    }

    /**
     * @param $year
     *
     * @return ExampleBuildingContent|null
     */
    public function getContentForYear($year)
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
     * @param int    $year build year
     * @param string $key  step->slug . form_element_name (dot notation)
     *
     * @return mixed|null
     */
    public function getExampleValueForYear($year, $key)
    {
        $content = $this->getContentForYear($year);

        if (! $content instanceof ExampleBuildingContent) {
            return null;
        }
        $content = $content->content;
        if (array_key_exists($key, $content)) {
            return $content[$key];
        }

        return null;
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
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMyCooperation($query)
    {
        $cooperationId = ! empty(HoomdossierSession::getCooperation()) ? HoomdossierSession::getCooperation() : 0;

        return $query->where('cooperation_id', '=', $cooperationId);
    }

    /**
     * Scope a query to only include buildings for my cooperation.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAnyOrMyCooperation($query)
    {
        $cooperationId = \Session::get('cooperation', 0);

        return $query->where('cooperation_id', '=', $cooperationId)->orWhereNull('cooperation_id');
    }

    /**
     * Scope on only generic example buildings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGeneric($query)
    {
        return $query->whereNull('cooperation_id');
    }
}
