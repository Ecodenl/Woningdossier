<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use App\Scopes\CooperationScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Step.
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property int $order
 * @property int|null $cooperation_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Step whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Step extends Model
{
    use TranslatableTrait;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        // for now, we keep it in kees.
//        static::addGlobalScope(new CooperationScope());
    }

    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    public function hasQuestionnaires()
    {
        if ($this->questionnaires()->count() > 0) {
            return true;
        }

        return false;
    }

}
