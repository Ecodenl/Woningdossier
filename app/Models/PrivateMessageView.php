<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessageView extends Model
{
    protected $fillable = [
        'private_message_id', 'user_id', 'cooperation_id', 'read_at'
    ];

    protected $dates = [
        'read_at'
    ];
}
