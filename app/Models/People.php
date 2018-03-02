<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\People
 *
 * @property-read \App\Models\LastNamePrefix $lastNamePrefix
 * @property-read \App\Models\Organisation $organisation
 * @property-read \App\Models\PersonType $personType
 * @property-read \App\Models\Title $title
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property int|null $organisation_id
 * @property int|null $last_name_prefix_id
 * @property int|null $type_id
 * @property int|null $title_id
 * @property string|null $date_of_birth
 * @property string $first_name_partner
 * @property string $last_name_partner
 * @property string|null $date_of_birth_partner
 * @property int $primary
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereDateOfBirthPartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereFirstNamePartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereLastNamePartner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereLastNamePrefixId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereOrganisationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\People whereUserId($value)
 */
class People extends Model
{
    //
	public function user(){
		return $this->belongsTo(User::class);
	}

	public function organisation(){
		return $this->belongsTo(Organisation::class);
	}

	public function lastNamePrefix(){
		return $this->belongsTo(LastNamePrefix::class);
	}

	public function personType(){
		return $this->belongsTo(PersonType::class);
	}

	public function title(){
		return $this->belongsTo(Title::class);
	}
}