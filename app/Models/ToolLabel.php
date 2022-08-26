<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ToolLabel extends Model
{
    use HasShortTrait, HasTranslations;

    public $translatables = [
        'name'
    ];
}
