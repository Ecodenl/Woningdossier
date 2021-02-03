<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasApiTokens, HasShortTrait;


    protected $fillable = ['name', 'short'];

    public function tokenCannot(string $ability): bool
    {
        return !$this->tokenCan($ability);
    }
}
