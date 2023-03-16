<?php

namespace App\Traits;

use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

trait RecordsLastToolChanged
{
    public static function bootRecordsLastToolChanged()
    {
        static::saved(function (Model $model) {
            $userId = $model->getAttribute('user_id');
            if (is_null($userId)) {
                $userId = $model->building->user_id;
            }

            User::where('id', $userId)->update(['tool_last_changed_at' => Carbon::now()]);
        });

        static::deleting(function (Model $model) {
            $userId = $model->getAttribute('user_id');
            if (is_null($userId)) {
                $userId = $model->building->user_id;
            }
            User::where('id', $userId)->update(['tool_last_changed_at' => Carbon::now()]);
        });
    }
}
