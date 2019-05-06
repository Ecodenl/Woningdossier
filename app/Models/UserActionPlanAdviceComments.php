<?php

namespace App\Models;

use App\Helpers\HoomdossierSession;
use App\Scopes\GetValueScope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class UserActionPlanAdviceComments extends Model
{
    use GetValueTrait, GetMyValuesTrait;

    protected $fillable = ['user_id', 'input_source_id', 'comment'];

}
