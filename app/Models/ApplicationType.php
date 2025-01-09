<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ApplicationType
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApplicationType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ApplicationType extends Model
{
}
