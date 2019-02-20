<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageLine extends \Spatie\TranslationLoader\LanguageLine
{
    protected $fillable = [
        'group', 'key', 'text', 'step_id', 'main_language_line_id', 'help_language_line_id'
    ];
}
