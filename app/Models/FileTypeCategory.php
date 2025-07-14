<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FileTypeCategory
 *
 * @property int $id
 * @property array<array-key, mixed> $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FileType> $fileTypes
 * @property-read int|null $file_types_count
 * @property-read mixed $translations
 * @method static Builder<static>|FileTypeCategory newModelQuery()
 * @method static Builder<static>|FileTypeCategory newQuery()
 * @method static Builder<static>|FileTypeCategory query()
 * @method static Builder<static>|FileTypeCategory whereCreatedAt($value)
 * @method static Builder<static>|FileTypeCategory whereId($value)
 * @method static Builder<static>|FileTypeCategory whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|FileTypeCategory whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static Builder<static>|FileTypeCategory whereLocale(string $column, string $locale)
 * @method static Builder<static>|FileTypeCategory whereLocales(string $column, array $locales)
 * @method static Builder<static>|FileTypeCategory whereName($value)
 * @method static Builder<static>|FileTypeCategory whereShort($value)
 * @method static Builder<static>|FileTypeCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileTypeCategory extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * Scope on the short column.
     *
     * @param $short
     */
    #[Scope]
    protected function short(Builder $query, $short): Builder
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
