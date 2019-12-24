<?php

namespace App\Models;

use App\Helpers\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Motivation.
 *
 * @property int                             $id
 * @property string                          $name
 * @property int|null                        $calculate_value
 * @property int                             $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation translated($attribute, $name, $locale = 'nl')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Motivation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Motivation extends Model
{
    use TranslatableTrait;

    /**
     *  Method to return the motivations, in order of the saved user motivations.
     *
     * @param User $user
     * @return Motivation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    public static function getInOrderOfUserMotivation(User $user)
    {
        $uMotivations = \DB::raw("(
            select * from user_motivations
	        where user_id = {$user->id}
        ) as user_motivations");

        // left join the user motivations so we can order it on the user motivations
        // and keep the not selected results aswell
        // we have to do this so we have the motivations in the same order as the saved user interests
        // unique on id because of how legacy, in previous versions it was possible to select the same motivation 4 times.
        return Motivation::leftJoin($uMotivations, 'motivations.id', '=', 'user_motivations.motivation_id')
            ->orderBy('user_motivations.order')
            ->select('motivations.name', 'motivations.id', 'user_motivations.order')
            ->get()->unique('id');

    }
}
