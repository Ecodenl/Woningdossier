<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PvPanelYield extends Model
{
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function orientation(){
    	return $this->belongsTo(PvPanelOrientation::class);
    }
}
