<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $type_id
 * @property string $email
 * @property int $primary
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EmailAddress whereUserId($value)
 * @mixin \Eloquent
 */
class EmailAddress extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}
}
