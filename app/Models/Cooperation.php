<?php

namespace App\Models;

use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Cooperation
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $website_url
 * @property string|null $cooperation_email
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CooperationMeasureApplication[] $cooperationMeasureApplications
 * @property-read int|null $cooperation_measure_applications_count
 * @property-read \Plank\Mediable\MediableCollection|\App\Models\CooperationSetting[] $cooperationSettings
 * @property-read int|null $cooperation_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExampleBuilding[] $exampleBuildings
 * @property-read int|null $example_buildings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Questionnaire[] $questionnaires
 * @property-read int|null $questionnaires_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Scan[] $scans
 * @property-read int|null $scans_count
 * @property-read \App\Models\CooperationStyle|null $style
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Plank\Mediable\MediableCollection|static[] all($columns = ['*'])
 * @method static \Database\Factories\CooperationFactory factory(...$parameters)
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereCooperationEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereHasMedia($tags = [], bool $matchAll = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereHasMediaMatchAll(array $tags)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation whereWebsiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation withMediaAndVariantsMatchAll($tags = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Cooperation withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @mixin \Eloquent
 */
class Cooperation extends Model
{
    use HasFactory, HasMedia;

    protected $fillable = [
        'name', 'slug', 'cooperation_email', 'website_url', 'econobis_wildcard', 'econobis_api_key',
    ];

    protected $hidden = [
        'econobis_api_key',
    ];


    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Model methods
    /**
     * Return the coaches from the current cooperation.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCoaches(): Collection
    {
        return $this->users()->forAllCooperations()->role('coach')->get();
    }

    /**
     * Return a collection of users for the cooperation and given role.
     *
     * This does not apply any scopes and should probably only be used in admin environments.
     */
    public function getUsersWithRole(Role $role): Collection
    {
        return User::hydrate(
            DB::table(config('permission.table_names.model_has_roles'))
                ->where('cooperation_id', $this->id)
                ->where('role_id', $role->id)
                ->leftJoin('users', config('permission.table_names.model_has_roles').'.'.config('permission.column_names.model_morph_key'), '=', 'users.id')
                ->get()->toArray()
        );
    }

    // Relations
    public function users(): HasMany
    {
        //TODO: Check if we can do this without cooperation global scope; the relation is called from the
        // cooperation so a session based cooperation scope seems pointless.
        return $this->hasMany(User::class);
    }

    public function buildings(): HasManyThrough
    {
        return $this->hasManyThrough(Building::class, User::class)
            ->withoutGlobalScope(CooperationScope::class);
    }

    public function scans(): BelongsToMany
    {
        return $this->belongsToMany(Scan::class)->using(CooperationScan::class);
    }

    public function cooperationMeasureApplications(): HasMany
    {
        return $this->hasMany(CooperationMeasureApplication::class);
    }

    public function style(): HasOne
    {
        return $this->hasOne(CooperationStyle::class);
    }

    public function questionnaires(): HasMany
    {
        return $this->hasMany(Questionnaire::class);
    }

    public function exampleBuildings(): HasMany
    {
        return $this->hasMany(ExampleBuilding::class);
    }

    public function cooperationSettings(): HasMany
    {
        return $this->hasMany(CooperationSetting::class);
    }
}
