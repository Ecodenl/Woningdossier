<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileStorage extends Model
{
    protected $fillable = [
        'cooperation_id', 'filename', 'user_id', 'file_type_id', 'content_type', 'is_being_processed', 'available_until',
    ];

    /**
     * Attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_being_processed' => 'bool',
        'available_until' => 'datetime'
    ];

    /**
     * Return the belongsto relationship on a cooperation.
     *
     * @return BelongsTo
     */
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }

    public function fileType()
    {
        return $this->belongsTo(FileType::class, 'file_type_id');
    }

    /**
     * Return the belongsto relationship on a user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific file is being processed
     *
     * @return bool
     */
    public function isBeingProcessed(): bool
    {
        return $this->is_being_proccessed;
    }
}
