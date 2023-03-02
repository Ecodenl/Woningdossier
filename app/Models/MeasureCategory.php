<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasureCategory extends Model
{
    use HasFactory, HasShortTrait, HasTranslations;

    public $fillable = ['name', 'short'];

    public $translatable = ['name'];
}
