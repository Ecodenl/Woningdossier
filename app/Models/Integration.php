<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory, HasShortTrait;
}
