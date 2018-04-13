<?php

namespace App\Model;

use App\Models\PvPanelOrientation;
use Illuminate\Database\Eloquent\Model;

class HeaterYield extends Model
{
	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function orientation(){
		return $this->belongsTo(PvPanelOrientation::class);
	}
}
