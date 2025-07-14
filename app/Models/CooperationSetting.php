<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use Plank\Mediable\MediableInterface;

/**
 * App\Models\CooperationSetting
 *
 * @property int $id
 * @property int $cooperation_id
 * @property string $short
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Cooperation $cooperation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Plank\Mediable\MediableCollection<int, static> all($columns = ['*'])
 * @method static \Plank\Mediable\MediableCollection<int, static> get($columns = ['*'])
 * @method static Builder<static>|CooperationSetting newModelQuery()
 * @method static Builder<static>|CooperationSetting newQuery()
 * @method static Builder<static>|CooperationSetting query()
 * @method static Builder<static>|CooperationSetting whereCooperationId($value)
 * @method static Builder<static>|CooperationSetting whereCreatedAt($value)
 * @method static Builder<static>|CooperationSetting whereHasMedia($tags = [], bool $matchAll = false)
 * @method static Builder<static>|CooperationSetting whereHasMediaMatchAll($tags)
 * @method static Builder<static>|CooperationSetting whereId($value)
 * @method static Builder<static>|CooperationSetting whereShort($value)
 * @method static Builder<static>|CooperationSetting whereUpdatedAt($value)
 * @method static Builder<static>|CooperationSetting whereValue($value)
 * @method static Builder<static>|CooperationSetting withMedia($tags = [], bool $matchAll = false, bool $withVariants = false)
 * @method static Builder<static>|CooperationSetting withMediaAndVariants($tags = [], bool $matchAll = false)
 * @method static Builder<static>|CooperationSetting withMediaAndVariantsMatchAll($tags = [])
 * @method static Builder<static>|CooperationSetting withMediaMatchAll(bool $tags = [], bool $withVariants = false)
 * @mixin \Eloquent
 */
class CooperationSetting extends Model implements Auditable, MediableInterface
{
    use HasMedia,
        \App\Traits\Models\Auditable;

    public $fillable = [
        'cooperation_id', 'short', 'value',
    ];

    # Model methods
    //

    # Attributes
    //

    # Scopes
    #[Scope]
    protected function forShort(Builder $query, string $short): Builder
    {
        return $query->where('short', $short);
    }

    # Relations
    public function cooperation(): BelongsTo
    {
        return $this->belongsTo(Cooperation::class);
    }
}
