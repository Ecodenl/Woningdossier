<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\FileTypeCategory
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileType[] $fileTypes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory short($short)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileTypeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileTypeCategory extends Model
{
    use TranslatableTrait;

    /**
     * Scope on the short column
     *
     * @param  Builder  $query
     * @param $short
     *
     * @return Builder
     */
    public function scopeShort(Builder $query, $short)
    {
        return $query->where('short', $short);
    }

    /**
     * Return the hasMany relationship on the filetypes
     *
     * @return HasMany
     */
    public function fileTypes(): HasMany
    {
        return $this->hasMany(FileType::class);
    }
}
