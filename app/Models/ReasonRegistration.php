<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ReasonRegistration
 *
 * @property int $reason_id
 * @property int $registration_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Reason $reason
 * @property-read \App\Models\Registration $registration
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReasonRegistration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReasonRegistration whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReasonRegistration whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ReasonRegistration whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ReasonRegistration extends Model
{
    //

	public function reason(){
		return $this->belongsTo(Reason::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}
}