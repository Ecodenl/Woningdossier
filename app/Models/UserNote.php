<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserNote
 *
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User $updatedBy
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $note
 * @property int|null $created_by_id
 * @property int|null $updated_by_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereUpdatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserNote whereUserId($value)
 */
class UserNote extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function createdBy(){
    	return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(){
    	return $this->belongsTo(User::class, 'updated_by_id');
    }



}
