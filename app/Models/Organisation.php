<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Organisation
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $type_id
 * @property string $name
 * @property string $website
 * @property string $chamber_of_commerce_number
 * @property string $vat_number
 * @property int|null $industry_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Industry|null $industry
 * @property-read \App\Models\OrganisationType $organisationType
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereChamberOfCommerceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereVatNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organisation whereWebsite($value)
 * @mixin \Eloquent
 */
class Organisation extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function organisationType(){
    	return $this->belongsTo(OrganisationType::class);
    }

    public function industry(){
    	return $this->belongsTo(Industry::class);
    }
}