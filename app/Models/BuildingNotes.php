<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingNotes extends Model
{
    protected $fillable = [
        'street',
        'number',
        'extension',
        'city',
        'postal_code',
        'country_code',
        'bag_addressid',
        'building_id',
        'note',
    ];
}
