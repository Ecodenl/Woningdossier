<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApplicationType.
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplicationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplicationType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplicationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplicationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ApplicationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApplicationType extends Model
{
}
