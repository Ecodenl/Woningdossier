<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationProcess extends Model
{
    use HasFactory;

    public $casts = [
        'synced_at' => 'datetime:Y-m-d H:i:s'
    ];
}
