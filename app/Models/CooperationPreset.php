<?php

namespace App\Models;

use App\Traits\HasShortTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\CooperationPreset
 *
 * @property int $id
 * @property string $title
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CooperationPresetContent[] $cooperationPresetContents
 * @property-read int|null $cooperation_preset_contents_count
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset query()
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CooperationPreset whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CooperationPreset extends Model
{
    use HasShortTrait;

    protected $fillable = [
        'title', 'short',
    ];

    public function cooperationPresetContents(): HasMany
    {
        return $this->hasMany(CooperationPresetContent::class);
    }

    public function getRouteKeyName()
    {
        return 'short';
    }
}