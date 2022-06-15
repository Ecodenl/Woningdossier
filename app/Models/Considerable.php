<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Considerable
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $input_source_id
 * @property string $considerable_type
 * @property int $considerable_id
 * @property bool $is_considering
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable forBuilding($building)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable forUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereConsiderableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereConsiderableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereIsConsidering($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Considerable whereUserId($value)
 * @mixin \Eloquent
 */
class Considerable extends Model
{
    use GetMyValuesTrait, GetValueTrait;

    protected $fillable = [
        'user_id',
        'input_source_id',
        'considerable_id',
        'considerable_type',
        'is_considering',
    ];

    protected $casts = [
        'is_considering' => true,
    ];
}
