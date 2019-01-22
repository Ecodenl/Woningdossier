<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class QuestionsAnswer extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = [
        'question_id', 'building_id', 'input_source_id', 'answer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function inputSource()
    {
        return $this->belongsTo(InputSource::class);
    }
}
