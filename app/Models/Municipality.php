<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use App\Traits\Models\HasMappings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Municipality
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\MunicipalityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Municipality extends Model
{
    use HasFactory,
        HasShortTrait,
        HasMappings;

    protected $fillable = [
        'name', 'short',
    ];

    public static function booted()
    {
        static::saving(function (Municipality $municipality) {
            // Upon creation, this isn't yet set
            if (! empty($municipality->short)) {
                $municipality->clearShortCache($municipality->short);
            }
            $municipality->short = Str::slug($municipality->name);
        });
    }
}
