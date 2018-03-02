<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereUpdatedAt($value)
 */
class TaskType extends Model
{
    public function tasks(){
    	return $this->hasMany(Task::class);
    }
}
