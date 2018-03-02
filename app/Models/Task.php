<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Task
 *
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User $finishedBy
 * @property-read \App\Models\Opportunity $opportunity
 * @property-read \App\Models\Registration $registration
 * @property-read \App\Models\User $responsibleUser
 * @property-read \App\Models\TaskType $type
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $task_type_id
 * @property int|null $user_id
 * @property int $status_id
 * @property int|null $registration_id
 * @property int|null $opportunity_id
 * @property string|null $date_planned
 * @property string|null $date_started
 * @property string|null $date_finished
 * @property int $responsible_user_id
 * @property int|null $finished_by_id
 * @property int $created_by_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDateFinished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDatePlanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDateStarted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereFinishedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereOpportunityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereResponsibleUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTaskTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUserId($value)
 */
class Task extends Model
{

	public function type(){
		return $this->belongsTo(TaskType::class);
	}

	public function user(){
		return $this->belongsTo(User::class);
	}

	public function registration(){
		return $this->belongsTo(Registration::class);
	}

	public function opportunity(){
		return $this->belongsTo(Opportunity::class);
	}

	public function responsibleUser(){
		return $this->belongsTo(User::class, 'responsible_user_id');
	}

	public function finishedBy(){
		return $this->belongsTo(User::class, 'finished_by_id');
	}

	public function createdBy(){
		return $this->belongsTo(User::class, 'created_by_id');
	}

}
