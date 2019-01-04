<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolSetting extends Model
{
    protected $fillable = [
        'changed_input_source_id', 'has_changed', 'building_id'
    ];

    protected $casts = [
        'has_changed' => 'bool'
    ];


    /**
     * check if its changed
     *
     * @return bool
     */
    public function hasChanged(): bool
    {
        return $this->has_changed;
    }
}
