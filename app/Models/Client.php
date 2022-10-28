<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Log[] $logs
 * @property-read int|null $logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Authenticatable
{
    use HasFactory;

    use HasApiTokens,
        HasShortTrait;

    protected $fillable = ['name', 'short'];

    # Model methods
    public function tokenCannot(string $ability): bool
    {
        return ! $this->tokenCan($ability);
    }

    # Relations
    public function logs(): MorphMany
    {
        return $this->morphMany(Log::class, 'loggable');
    }
}
