<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Registration
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Opportunity[] $opportunities
 * @property-read \App\Models\RegistrationStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @mixin \Eloquent
 * @property int $id
 * @property int $address_id
 * @property int|null $registration_status_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereRegistrationStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereUpdatedAt($value)
 */
class Registration extends Model
{
    //
	public function status(){
		return $this->belongsTo(RegistrationStatus::class);
	}

	public function opportunities(){
		return $this->hasMany(Opportunity::class);
	}

	public function tasks(){
		return $this->hasMany(Task::class);
	}
}
