<?php

namespace App\Services;

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

        $user->considerable(get_class($considerable))->sync([
            $considerable->id => $considerableData
        ]);

    }
}
