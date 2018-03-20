<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RegistrationSource
 *
 * @property int $source_id
 * @property int $registration_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Registration $registration
 * @property-read \App\Models\Source $source
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RegistrationSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RegistrationSource whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RegistrationSource whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\RegistrationSource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RegistrationSource extends Model
{
	public function source(){
		return $this->belongsTo(Source::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}

}
