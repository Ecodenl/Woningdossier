<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Interest
 *
 * @property int $id
 * @property string $translation_key
 * @property int $calculate_value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereTranslationKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Interest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Interest extends Model
{
    //
}
