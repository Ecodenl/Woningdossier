<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int $building_id
 * @property string $short
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Building|null $building
 * @method static Builder<static>|BuildingSetting forShort(string $short)
 * @method static Builder<static>|BuildingSetting newModelQuery()
 * @method static Builder<static>|BuildingSetting newQuery()
 * @method static Builder<static>|BuildingSetting query()
 * @method static Builder<static>|BuildingSetting whereBuildingId($value)
 * @method static Builder<static>|BuildingSetting whereCreatedAt($value)
 * @method static Builder<static>|BuildingSetting whereId($value)
 * @method static Builder<static>|BuildingSetting whereShort($value)
 * @method static Builder<static>|BuildingSetting whereUpdatedAt($value)
 * @method static Builder<static>|BuildingSetting whereValue($value)
 * @mixin \Eloquent
 */
class BuildingSetting extends Model implements Auditable
{
    use \App\Traits\Models\Auditable;

    public $fillable = [
        'building_id', 'short', 'value',
    ];

    # Scopes
    #[Scope]
    protected function forShort(Builder $query, string $short): Builder
    {
        return $query->where('short', $short);
    }

    # Relations
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
