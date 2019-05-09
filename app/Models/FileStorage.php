<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileStorage extends Model
{
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

    /**
     * Return the belongsto relationship on a user.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
