<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use TranslatableTrait;

    protected $fillable = [
        'question_id', 'name'
    ];
}
