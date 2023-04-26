<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DossierSetting extends Model
{
    use HasFactory, GetMyValuesTrait, GetValueTrait;

    public $fillable = ['building_id', 'input_source_id', 'type', 'done_at'];
}
