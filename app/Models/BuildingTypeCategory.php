<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class BuildingTypeCategory extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
}
