<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CustomMeasureApplication extends Model
{
    use HasTranslations, GetMyValuesTrait, GetValueTrait;

    public $translatable = ['name', 'info'];

    protected $fillable = ['building_id', 'input_source_id', 'name', 'info', 'extra', 'costs', 'savings_money'];

    protected $casts = [
        'costs' => 'array',
        'extra' => 'array'
    ];
}
