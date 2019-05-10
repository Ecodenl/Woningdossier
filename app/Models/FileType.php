<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            $q->withExpired()->where('is_being_processed', true);
        })->where('id', $this->id)->first() instanceof FileType;
    }
}
