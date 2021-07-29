<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
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
 * @property string $content_type
 * @property \Illuminate\Support\Carbon|null $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FileTypeCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FileStorage[] $files
 * @property-read int|null $files_count
 * @method static \Illuminate\Database\Eloquent\Builder|FileType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileType query()
 * @method static \Illuminate\Database\Eloquent\Builder|FileType translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereFileTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FileType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FileType extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * Attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'short';
    }

    /**
     * Return the belongsto relationship on a categort.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FileTypeCategory::class);
    }

    /**
     * Return the hasMany relationship on the file storage.
     */
    public function files(): HasMany
    {
        return $this->hasMany(FileStorage::class);
    }

    /**
     * Check if the filetype has a file that is being processed.
     */
    public function isBeingProcessed(): bool
    {
        return FileType::whereHas('files', function ($q) {
            return $q->withExpired()->beingProcessed();
        })->where('id', $this->id)->first() instanceof FileType;
    }

    /**
     * Check if a questionnaire is being processed.
     */
    public function isQuestionnaireBeingProcessed(Questionnaire $questionnaire): bool
    {
        return FileType::whereHas('files', function ($q) use ($questionnaire) {
            return $q->withExpired()->beingProcessed()->where('questionnaire_id', $questionnaire->id);
        })->where('id', $this->id)->first() instanceof FileType;
    }
}
