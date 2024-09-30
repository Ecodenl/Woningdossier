<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Interest
 *
 * @property int $id
 * @property array $name
 * @property int $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Step> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\InterestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Interest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Interest query()
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Interest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Interest extends Model
{
    use HasFactory;

    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'interest', 'user_interests');
    }

    public function steps(): MorphToMany
    {
        return $this->morphedByMany(Step::class, 'interest', 'user_interests');
    }
}
