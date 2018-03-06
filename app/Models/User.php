<?php

namespace App\Models;

use App\Events\UserCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

/**
 * App\Models\User
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AddressUserUsage[] $addressUsage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Address[] $addresses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailAddress[] $emailAddresses
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserEnergyHabit[] $energyHabits
 * @property-read \App\Models\LastNamePrefix $lastNamePrefix
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserNote[] $notes
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Opportunity[] $opportunities
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organisation[] $organisations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\People[] $people
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PhoneNumber[] $phoneNumbers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\Title $title
 * @mixin \Eloquent
 * @property int $id
 * @property string $first_name
 * @property int|null $last_name_prefix_id
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $title_id
 * @property string $phone_number
 * @property string $mobile
 * @property string $occupation
 * @property string|null $last_visit
 * @property int $visit_count
 * @property int $active
 * @property string|null $confirm_token
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereConfirmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastNamePrefixId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastVisit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereVisitCount($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Cooperation[] $cooperations
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone_number',
	    'confirm_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lastNamePrefix(){
    	return $this->belongsTo(LastNamePrefix::class);
    }

    public function title(){
    	return $this->belongsTo(Title::class);
    }

    public function addresses(){
    	return $this->hasMany(Address::class);
    }

    public function phoneNumbers(){
    	return $this->hasMany(PhoneNumber::class);
    }

    public function emailAddresses(){
    	return $this->hasMany(EmailAddress::class);
    }

    public function people(){
    	return $this->hasMany(People::class);
    }

    public function organisations(){
    	return $this->hasMany(Organisation::class);
    }

    public function notes(){
    	return $this->hasMany(UserNote::class);
    }

    public function addressUsage(){
    	return $this->hasMany(AddressUserUsage::class);
    }

    public function energyHabits(){
    	return $this->hasMany(UserEnergyHabit::class);
    }

    public function opportunities(){
    	return $this->hasMany(Opportunity::class);
    }

	public function tasks(){
		return $this->hasMany(Task::class);
	}

	/**
	 * The cooperations the user is associated with
	 */
	public function cooperations(){
    	return $this->belongsToMany(Cooperation::class);
	}

	/**
	 * Returns whether or not a user is associated with a particular Cooperation
	 * @param Cooperation $cooperation
	 *
	 * @return bool
	 */
	public function isAssociatedWith(Cooperation $cooperation){
		return $this->cooperations()
		            ->where('id', $cooperation->id)
		            ->count() > 0;
	}

}
