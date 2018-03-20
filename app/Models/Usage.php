<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Usage
 *
 * @property int $id
 * @property string|null $start_period
 * @property string|null $end_period
 * @property int|null $usage
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereEndPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereStartPeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Usage whereUsage($value)
 * @mixin \Eloquent
 */
class Usage extends Model
{

}
