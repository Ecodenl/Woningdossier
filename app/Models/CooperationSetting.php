<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Plank\Mediable\Mediable;

/**
 * App\Models\CooperationSetting
 *
 * @property int $id
 * @property int $cooperation_id
 * @property string $short
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Media[] $media
 * @property-read int|null $media_count
 * @method static \Plank\Mediable\MediableCollection|static[] all($columns = ['*'])
 * @method static Builder|CooperationSetting forShort(string $short)
 * @method static \Plank\Mediable\MediableCollection|static[] get($columns = ['*'])
 * @method static Builder|CooperationSetting newModelQuery()
 * @method static Builder|CooperationSetting newQuery()
 * @method static Builder|CooperationSetting query()
 * @method static Builder|CooperationSetting whereCooperationId($value)
 * @method static Builder|CooperationSetting whereCreatedAt($value)
 * @method static Builder|CooperationSetting whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder|CooperationSetting whereHasMediaMatchAll(array $tags)
 * @method static Builder|CooperationSetting whereId($value)
 * @method static Builder|CooperationSetting whereShort($value)
 * @method static Builder|CooperationSetting whereUpdatedAt($value)
 * @method static Builder|CooperationSetting whereValue($value)
 * @method static Builder|CooperationSetting withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder|CooperationSetting withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder|CooperationSetting withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder|CooperationSetting withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @mixin \Eloquent
 */
class CooperationSetting extends Model
{
    use Mediable;

    public $fillable = [
        'cooperation_id', 'short', 'value',
    ];

    # Model methods
    //

    # Attributes
    //

    # Scopes
    public function scopeForShort(Builder $query, string $short): Builder
    {
        return $query->where('short', $short);
    }

    # Relations
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }
}
