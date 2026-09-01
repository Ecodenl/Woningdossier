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

class CreateUserAccount implements ShouldQueue
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
            Log::debug('SmartTwin calls are disabled, skipping CreateUserAccount for user ' . $this->user->id);
            return;
        }

        $account = $this->user->account;

        // SmartTwin identifies a user by e-mail address and returns a 400 for one that already exists.
        // The e-mail address belongs to the account, so the link does too — a second user on the same
        // account (another cooperation) reuses it instead of trying to create a duplicate.
        if (! $account instanceof Account || ! empty($account->smartTwinUserId())) {
            return;
        }

        try {
            $response = $api->user()->create(
                $account->email,
                $this->user->first_name ?? '',
                $this->user->last_name ?? '',
                UserRole::Resident,
            );
        } catch (ClientException $e) {
            // A 4xx is deterministic: retrying sends the identical request and gets the identical
            // answer. SmartTwin returns a bare 400 for an e-mail address that already has an account,
            // with no machine-readable reason, so log the body and give up rather than burn the tries.
            $this->failWithResponse($e, $account);

            return;
        }

        $userId = $response['userId'] ?? null;
        if (empty($userId)) {
            Log::warning('SmartTwin returned no userId for CreateUserAccount', [
                'user_id'    => $this->user->id,
                'account_id' => $account->id,
            ]);

            return;
        }

        $account->linkSmartTwinUser($userId, UserRole::Resident);
    }
}
