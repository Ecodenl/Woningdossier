<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

/**
 * App\Models\Scan
 *
 * @property-read array $translations
 * @method static Builder|Scan bySlug(string $slug, string $locale = 'nl')
 * @method static Builder|Scan newModelQuery()
 * @method static Builder|Scan newQuery()
 * @method static Builder|Scan query()
 * @mixin \Eloquent
 */
class Scan extends Model
{
    use HasTranslations;

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

    // TODO: Slug trait?
    public function scopeBySlug(Builder $query, string $slug, string $locale = 'nl'): Builder
    {
        return $query->where("slug->{$locale}", $slug);
    }
}
