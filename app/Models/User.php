<?php

namespace App\Models;

use App\Events\UserCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

}
