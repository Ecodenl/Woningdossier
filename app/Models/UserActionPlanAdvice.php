<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActionPlanAdvice extends Model
{

	public $fillable = [
		'user_id', 'measure_application_id', // old
		'costs', 'savings_gas', 'savings_electricity', 'savings_money',
		'year',
	];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function measureApplication(){
    	return $this->belongsTo(MeasureApplication::class);
    }

    public function step(){
    	return $this->belongsTo(Step::class);
    }

    public static function getCategorizedActionPlan(User $user){
    	$result = [];
    	$advices = self::where('user_id', $user->id)
	                   ->orderBy('step_id', 'asc')
	                   ->orderBy('year', 'asc')
	                   ->get();
    	/** @var UserActionPlanAdvice $advice */
	    foreach($advices as $advice){
	    	/** @var MeasureApplication $measureApplication */
			$measureApplication = $advice->measureApplication;
			if (!array_key_exists($measureApplication->measure_type, $result)){
				$result[$measureApplication->measure_type] = [];
			}
			if (!array_key_exists($advice->step->name, $result[$measureApplication->measure_type])){
				$result[$measureApplication->measure_type][$advice->step->name] = [];
			}

			$result[$measureApplication->measure_type][$advice->step->name][]= $advice;
	    }

    	return $result;
    }

	/**
	 * Scope a query to only include the current user.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeForMe($query)
	{
		return $query->where('user_id', \Auth::id());
	}

	/**
	 * Scope a query to only include results for the particular step.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param Step $step
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeForStep($query, Step $step){
		return $query->where('step_id', $step->id);
	}
}
