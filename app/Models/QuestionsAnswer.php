<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionsAnswer extends Model
{
    protected $fillable = [
        'question_id', 'building_id', 'input_source_id', 'answer'
    ];
}
