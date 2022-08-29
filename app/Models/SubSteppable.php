<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class SubSteppable extends MorphPivot
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
