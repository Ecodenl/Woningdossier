<?php

namespace App\Models;

use App\Traits\Models\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Motivation
 *
 * @property int $id
 * @property array $name
 * @property int|null $calculate_value
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translations
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereCalculateValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereJsonContainsLocale(string $column, string $locale, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereJsonContainsLocales(string $column, array $locales, ?mixed $value, string $operand = '=')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereLocale(string $column, string $locale)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereLocales(string $column, array $locales)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Motivation extends Model
{
    use HasTranslations;

    protected $translatable = [
        'name',
    ];

    /**
     * TODO: DB::raw like this is probably still supported, however, the code isn't used. Not tested.
     *
     * Method to return the motivations, in order of the saved user motivations.
     *
     * @return Motivation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    //public static function getInOrderOfUserMotivation(User $user)
    //{
    //    $uMotivations = \DB::raw("(
    //        select * from user_motivations
    //        where user_id = {$user->id}
    //    ) as user_motivations");
    //
    //    // left join the user motivations so we can order it on the user motivations
    //    // and keep the not selected results aswell
    //    // we have to do this so we have the motivations in the same order as the saved user interests
    //    // unique on id because of how legacy, in previous versions it was possible to select the same motivation 4 times.
    //    return Motivation::leftJoin($uMotivations, 'motivations.id', '=', 'user_motivations.motivation_id')
    //        ->orderBy('user_motivations.order')
    //        ->select('motivations.name', 'motivations.id', 'user_motivations.order')
    //        ->get()->unique('id');
    //}
}
