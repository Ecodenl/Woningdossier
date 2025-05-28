<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Integration
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\TFactory|null $use_factory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Integration whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Integration extends Model
{
    use HasFactory, HasShortTrait;
}
