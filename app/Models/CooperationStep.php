<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CooperationStep extends Model
{
    protected $fillable = [
        'is_active', 'order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
