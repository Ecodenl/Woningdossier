<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SubSteppable extends Pivot
{
    protected $table = 'sub_steppables';

    protected $casts = [
        'conditions' => 'array',
    ];

    public function subSteppables()
    {
        return $this->morphTo();
    }
}
