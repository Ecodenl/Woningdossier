<?php

namespace App\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;

/**
 * Thrown when the Verbeterjehuis API - which sits behind a Cloudflare WAF - blocks us
 * with a 403 (or 408/429 when rate-limited) and returns an HTML block page instead of
 * the usual JSON.
 *
 * This is a genuine failure to refresh, so the job is allowed to fail honestly. The
 * catch is that a single block hits every queued advice at once, which would otherwise
 * flood Sentry with thousands of identical events. The throttling lives in this
 * exception's report() method (see below) so it is independent of any third-party
 * (Sentry) configuration that could be republished or upgraded.
 */
class VerbeterjehuisWafBlockException extends Exception
{
    /**
     * How long a single block report suppresses follow-up reports for.
     */
    private const int HEARTBEAT_MINUTES = 60;

    private const string HEARTBEAT_KEY = 'verbeterjehuis-waf-block-heartbeat';

    public function __construct(ClientException $previous)
    {
        $statusCode = $previous->getResponse()?->getStatusCode();

        parent::__construct(
            "Verbeterjehuis Cloudflare WAF blocked the regulation request (HTTP {$statusCode}).",
            $previous->getCode(),
            $previous
        );
    }

    /**
     * Custom reporting hook that Laravel calls automatically.
     *
     * While a WAF block is active every queued advice raises this exception, so we only
     * want a heartbeat in Sentry, not one event per job. Cache::add is atomic, so only
     * the first occurrence within the window wins the slot.
     *
     * Returning false tells Laravel to fall through to the default reporter (Sentry);
     * returning true marks the exception as handled, suppressing the report.
     */
    public function report(): bool
    {
        $firstInWindow = Cache::add(self::HEARTBEAT_KEY, true, now()->addMinutes(self::HEARTBEAT_MINUTES));

        return ! $firstInWindow;
    }
}
