<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Municipality
 *
 * @property int $id
 * @property string $name
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\MunicipalityFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Municipality whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Municipality extends Model
{
    use HasFactory, HasShortTrait;

    protected $fillable = [
        'name',
        'short'
    ];
}
