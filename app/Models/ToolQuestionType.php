<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolQuestionType extends Model
{
    protected $fillable = [
        'name',
        'short'
    ];

    protected $casts = [
        'name' => 'array'
    ];
}
