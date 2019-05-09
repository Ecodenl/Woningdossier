<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
