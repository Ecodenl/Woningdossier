<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileTypeCategory extends Model
{
    use TranslatableTrait;

    /**
     * Return the hasMany relationship on the filetypes
     *
     * @return HasMany
     */
    public function fileType(): HasMany
    {
        return $this->hasMany(FileType::class);
    }
}
