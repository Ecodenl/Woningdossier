<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'conditions',
        'from_model_type',
        'from_model_id',
        'from_value',

        'target_model_type',
        'target_model_id',
        'target_value',
        'target_data'
    ];

    protected $casts = [
        'target_data' => 'array'
    ];

    public function mapable()
    {
        return $this->morphTo('target_model');
    }
}
