<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
