<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class CooperationMeasureApplication extends Model
{
    use HasTranslations;

    protected $translatable = ['name', 'info'];

    protected $casts = [
        'costs' => 'json',
        'extra' => 'icon',
    ];
}
