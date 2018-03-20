<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Registration
 *
 * @property int $id
 * @property int $building_id
 * @property int|null $registration_status_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Opportunity[] $opportunities
 * @property-read \App\Models\RegistrationStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereRegistrationStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Registration whereUpdatedAt($value)
 * @mixin \Eloquent
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
