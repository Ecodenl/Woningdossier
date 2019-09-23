<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\FileType
 *
 * @property int $id
 * @property int $file_type_category_id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FileTypeCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileStorage[] $files
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereFileTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FileType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileType extends Model
{
    use TranslatableTrait;

    /**
     * Attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'datetime'
    ];

    public function getRouteKeyName()
    {
        return 'short';
    }

    /**
     * Return the belongsto relationship on a categort
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FileTypeCategory::class);
    }

    /**
     * Return the hasMany relationship on the file storage
     *
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(FileStorage::class);
    }

    /**
     * Check if the filetype has a file that is being processed.
     *
     * @return bool
     */
    public function isBeingProcessed(): bool
    {
        return FileType::whereHas('files', function ($q) {
            $q->withExpired()->beingProcessed();
        })->where('id', $this->id)->first() instanceof FileType;
    }
}
