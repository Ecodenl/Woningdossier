<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PhoneNumber
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $type_id
 * @property string $number
 * @property int $primary
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PhoneNumber whereUserId($value)
 * @mixin \Eloquent
 */
class PhoneNumber extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}
}
