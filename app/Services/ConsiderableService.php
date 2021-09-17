<?php

namespace App\Services;

use App\Models\Considerable;
use App\Models\InputSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ConsiderableService
{
    /**
     * Method to save the consideration for the user
     */
    public static function save(Model $considerable, User $user, InputSource $inputSource, $considerableData)
    {
        Log::debug('Saving the user considerable');
        // todo; log more data, whether he's considering it or not yadadad

        // cant use sync because of the events and nature of eloquent handling pivot tables.
        Considerable::updateOrCreate(
            [
                'user_id' => $user->id,
                'input_source_id' => $inputSource->id,
                'considerable_id' => $considerable->id,
                'considerable_type' => get_class($considerable)
            ],
            $considerableData
        );

    }
}
