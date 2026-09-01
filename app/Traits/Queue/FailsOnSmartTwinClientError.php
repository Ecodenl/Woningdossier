<?php

namespace App\Traits\Queue;

use App\Models\Account;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

/**
 * SmartTwin answers a 4xx deterministically: the same request gets the same rejection, so the
 * remaining $tries are wasted. Their error body carries no machine-readable reason — a duplicate
 * e-mail address and a malformed payload both come back as a bare 400 — so the only useful thing
 * to do is record the response verbatim and fail the job right away.
 */
trait FailsOnSmartTwinClientError
{
    protected function failWithResponse(ClientException $e, Account $account): void
    {
        $response = $e->getResponse();

        Log::error('SmartTwin rejected ' . static::class, [
            'user_id'    => $this->user->id ?? null,
            'account_id' => $account->id,
            'status'     => $response->getStatusCode(),
            'body'       => (string) $response->getBody(),
        ]);

        $this->fail($e);
    }
}
