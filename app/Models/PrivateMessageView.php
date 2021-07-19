<?php

namespace App\Models;

use App\Helpers\Hoomdossier;
use App\Helpers\HoomdossierSession;
use App\Traits\GetMyValuesTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PrivateMessageView
 *
 * @property int $id
 * @property int $private_message_id
 * @property int|null $user_id
 * @property int|null $input_source_id
 * @property int|null $to_cooperation_id
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\InputSource|null $inputSource
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView allInputSources()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView forBuilding(\App\Models\Building $building)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView forCurrentInputSource()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView forInputSource(\App\Models\InputSource $inputSource)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView forMe(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView residentInput()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereInputSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView wherePrivateMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereToCooperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivateMessageView whereUserId($value)
 * @mixin \Eloquent
 */
class PrivateMessageView extends Model
{
    use GetMyValuesTrait;

    protected $fillable = [
        'input_source_id', 'private_message_id', 'user_id', 'to_cooperation_id', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Query to scope records for the current input source.
     *
     * Normally we would use the GetValueTrait which applies the global GetValueScope.
     *
     * BUT: the input_source_id will sometimes be empty (coordinator and cooperation-admin), so we cant use the global scope.
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeForCurrentInputSource($query)
    {
        return $query->where('input_source_id', HoomdossierSession::getInputSourceValue());
    }

    /**
     * Check if a private message is left unread.
     *
     * @param $privateMessage
     */
    public static function isMessageUnread($privateMessage): bool
    {
        // if the user is logged in as a coordinator or cooperation admin
        if (\App\Helpers\Hoomdossier::user()->hasRoleAndIsCurrentRole(['coordinator', 'cooperation-admin'])) {
            $privateMessageView = static::where('private_message_id', $privateMessage->id)
                                        ->where('to_cooperation_id', HoomdossierSession::getCooperation())->first();
            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        } else {
            $privateMessageView = static::where('private_message_id', $privateMessage->id)
                                        ->forCurrentInputSource()
                                        ->where('user_id', Hoomdossier::user()->id)
                                        ->first();

            if ($privateMessageView instanceof PrivateMessageView && is_null($privateMessageView->read_at)) {
                return true;
            }

            return false;
        }
    }
}
