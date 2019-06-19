<?php

namespace App\Models;

use App\Scopes\AvailableScope;
use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileStorage extends Model
{

    public static function boot()
    {
        parent::boot();
        
        static::addGlobalScope(new AvailableScope);
        static::addGlobalScope(new CooperationScope);
    }
    
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
     * Query to scope the expired files.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithExpired(Builder $query)
    {
        return $query->withoutGlobalScope(AvailableScope::class);
    }
    
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
        return $this->is_being_processed;
    }

    /**
     * Method that's used when the file is done processing.
     */
    public function isProcessed()
    {
        $availableUntil = $this->created_at->addDays($this->fileType->duration ?? 7);
        $this->available_until = $availableUntil;
        $this->is_being_processed = false;
        $this->save();
    }

}
