<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasTranslations;

    protected $translatable = [
        'text',
    ];

    protected $casts = [
        'conditions' => 'json'
    ];
}
