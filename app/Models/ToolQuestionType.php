<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ToolQuestionType extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
    ];

    protected $fillable = [
        'name',
        'short'
    ];

    protected $casts = [
        'name' => 'array'
    ];
}
