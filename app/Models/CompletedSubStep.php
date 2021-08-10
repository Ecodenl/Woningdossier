<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedSubStep extends Model
{
    protected $fillable = ['sub_step_id', 'building_id', 'input_source_id'];
}
