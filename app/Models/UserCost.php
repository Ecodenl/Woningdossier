<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\UserCost
 *
 * @property int $id
 * @property int $user_id
 * @property int $input_source_id
 * @property string $advisable_type
 * @property int $advisable_id
 * @property int|null $own_total
 * @property int|null $subsidy_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $advisable
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\InputSource $inputSource
 * @property-read \App\Models\User $user
 * @method static Builder<static>|UserCost allInputSources()
 * @method static Builder<static>|UserCost forAdvisable(\Illuminate\Database\Eloquent\Model $advisable)
 * @method static Builder<static>|UserCost forBuilding(\App\Models\Building|int $building)
 * @method static Builder<static>|UserCost forInputSource(\App\Models\InputSource $inputSource)
 * @method static Builder<static>|UserCost forMe(?\App\Models\User $user = null)
 * @method static Builder<static>|UserCost forUser(\App\Models\User|int $user)
 * @method static Builder<static>|UserCost newModelQuery()
 * @method static Builder<static>|UserCost newQuery()
 * @method static Builder<static>|UserCost query()
 * @method static Builder<static>|UserCost residentInput()
 * @method static Builder<static>|UserCost whereAdvisableId($value)
 * @method static Builder<static>|UserCost whereAdvisableType($value)
 * @method static Builder<static>|UserCost whereCreatedAt($value)
 * @method static Builder<static>|UserCost whereId($value)
 * @method static Builder<static>|UserCost whereInputSourceId($value)
 * @method static Builder<static>|UserCost whereOwnTotal($value)
 * @method static Builder<static>|UserCost whereSubsidyTotal($value)
 * @method static Builder<static>|UserCost whereUpdatedAt($value)
 * @method static Builder<static>|UserCost whereUserId($value)
 * @mixin \Eloquent
 */
class UserCost extends Model implements Auditable
{
    use GetValueTrait,
        GetMyValuesTrait,
        \App\Traits\Models\Auditable;

    public $fillable = [
        'user_id', 'input_source_id', 'advisable_type', 'advisable_id', 'own_total', 'subsidy_total',
    ];

    # Scopes
    #[Scope]
    protected function forAdvisable(Builder $query, Model $advisable): Builder
    {
        return $query->where('advisable_type', get_class($advisable))
            ->where('advisable_id', $advisable->id);
    }

    # Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advisable(): MorphTo
    {
        return $this->morphTo();
    }
}
