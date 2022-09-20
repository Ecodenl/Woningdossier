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

    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
}
