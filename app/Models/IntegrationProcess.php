<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'integration_id',
        'building_id',
        'process',
        'synced_at',
    ];
    public $casts = [
        'synced_at' => 'datetime:Y-m-d H:i:s'
    ];
}
