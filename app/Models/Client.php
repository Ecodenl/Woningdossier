<?php

namespace App\Models;

use App\Traits\HasApiTokens;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasApiTokens, HasShortTrait;
}
