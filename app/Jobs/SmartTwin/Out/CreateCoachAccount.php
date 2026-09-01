<?php

namespace App\Jobs\SmartTwin\Out;

use App\Helpers\Hoomdossier;
use App\Helpers\Queue;
use App\Models\Account;
use App\Models\User;
use App\Services\SmartTwin\Api\SmartTwinApi;
use App\Services\SmartTwin\Api\UserRole;
use App\Traits\Queue\FailsOnSmartTwinClientError;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateCoachAccount implements ShouldQueue
{
    use Dispatchable, FailsOnSmartTwinClientError, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public User $user)
    {
        $this->queue = Queue::APP_EXTERNAL;
    }

    public function handle(SmartTwinApi $api): void
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            Log::debug('SmartTwin calls are disabled, skipping CreateCoachAccount for user ' . $this->user->id);
            return;
        }

        $account = $this->user->account;

        // See CreateUserAccount: one e-mail address is one SmartTwin user, and the e-mail address
        // belongs to the account, so the link is stored there rather than per user.
        if (! $account instanceof Account || ! empty($account->smartTwinUserId())) {
            return;
        }

        try {
            $response = $api->user()->create(
                $account->email,
                $this->user->first_name ?? '',
                $this->user->last_name ?? '',
                UserRole::Advisor,
            );
        } catch (ClientException $e) {
            // See CreateUserAccount: a 4xx will not change on a retry, so record it and stop.
            $this->failWithResponse($e, $account);

            return;
        }

        $userId = $response['userId'] ?? null;
        if (empty($userId)) {
            Log::warning('SmartTwin returned no userId for CreateCoachAccount', [
                'user_id'    => $this->user->id,
                'account_id' => $account->id,
            ]);

            return;
        }

        $account->linkSmartTwinUser($userId, UserRole::Advisor);
    }
}
