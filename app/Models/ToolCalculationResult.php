<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ToolCalculationResult extends Model
{
    use HasTranslations, HasShortTrait;

    protected $translatable = [
        'name',
        'help_text'
    ];
}
