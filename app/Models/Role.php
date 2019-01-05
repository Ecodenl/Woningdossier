<?php

namespace App\Models;

class Role extends \Spatie\Permission\Models\Role
{
    /**
     * Return the input source for the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inputSource()
    {
        return $this->belongsTo('App\Models\InputSource', 'input_source_id', 'id');
    }
}
