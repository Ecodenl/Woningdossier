<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Opportunity
 *
 * @property int $id
 * @property int $measure_id
 * @property int $user_id
 * @property string $number
 * @property int|null $registration_id
 * @property int|null $campaign_id
 * @property string|null $quotation_text
 * @property string|null $desired_date
 * @property int|null $created_by_id
 * @property int|null $owned_by_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Campaign|null $campaign
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\Measure $measure
 * @property-read \App\Models\User|null $ownedBy
 * @property-read \App\Models\Registration|null $registration
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereDesiredDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereMeasureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereOwnedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereQuotationText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereRegistrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Opportunity whereUserId($value)
 * @mixin \Eloquent
 */
class Opportunity extends Model
{
    public function measure(){
    	return $this->belongsTo(Measure::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function registration(){
    	return $this->belongsTo(Registration::class);
    }

    public function campaign(){
    	return $this->belongsTo(Campaign::class);
    }

    public function createdBy(){
    	return $this->belongsTo(User::class, 'created_by_id');
    }

    public function ownedBy(){
    	return $this->belongsTo(User::class, 'owned_by_id');
    }

	public function tasks(){
		return $this->hasMany(Task::class);
	}

}
