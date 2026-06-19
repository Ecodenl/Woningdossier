<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Models\User;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\Api\UserRole;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateUserAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public User $user)
    {
        $this->queue = Queue::APP_EXTERNAL;
    }

    public function handle(SmartTwinApi $api): void
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            Log::debug('SmartTwin calls are disabled, skipping CreateUserAccount for user ' . $this->user->id);
            return;
        }

        if (! empty($this->user->extra['smarttwin_user_id'] ?? null)) {
            return;
        }

        $response = $api->user()->create(
            $this->user->account->email,
            $this->user->first_name ?? '',
            $this->user->last_name ?? '',
            UserRole::Resident,
        );

        $userId = $response['userId'] ?? null;
        if (! empty($userId)) {
            $extra = $this->user->extra ?? [];
            $extra['smarttwin_user_id'] = $userId;
            $this->user->extra = $extra;
            $this->user->save();
        }
    }
}
