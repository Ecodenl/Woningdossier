<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class Considerable extends Model
{
    use GetMyValuesTrait, GetValueTrait;

    protected $fillable = [
        'user_id',
        'input_source_id',
        'considerable_id',
        'considerable_type',
        'is_considering',
    ];

    protected $casts = [
        'is_considering' => true,
    ];
}
