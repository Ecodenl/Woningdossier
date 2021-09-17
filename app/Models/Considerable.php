<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class Considerable extends Model
{
    use GetMyValuesTrait, GetValueTrait;

    protected $casts = [
        'is_considering' => true,
    ];
}
