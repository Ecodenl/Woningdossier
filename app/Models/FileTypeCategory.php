<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FileTypeCategory
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileType[] $fileTypes
 * @property-read int|null $file_types_count
 * @method static Builder|FileTypeCategory newModelQuery()
 * @method static Builder|FileTypeCategory newQuery()
 * @method static Builder|FileTypeCategory query()
 * @method static Builder|FileTypeCategory short($short)
 * @method static Builder|FileTypeCategory translated($attribute, $name, $locale = 'nl')
 * @method static Builder|FileTypeCategory whereCreatedAt($value)
 * @method static Builder|FileTypeCategory whereId($value)
 * @method static Builder|FileTypeCategory whereName($value)
 * @method static Builder|FileTypeCategory whereShort($value)
 * @method static Builder|FileTypeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileTypeCategory extends Model
{
    use TranslatableTrait;

    /**
     * Scope on the short column.
     *
     * @param $short
     *
     * @return Builder
     */
    public function scopeShort(Builder $query, $short)
    {
        return $query->where('short', $short);
    }

    /**
     * Return the hasMany relationship on the filetypes.
     */
    public function fileTypes(): HasMany
    {
        return $this->hasMany(FileType::class);
    }
}
