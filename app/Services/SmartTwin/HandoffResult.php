<?php

namespace App\Services\SmartTwin;

/**
 * Outcome of a SmartTwin SSO handoff attempt.
 *
 * On success it carries the deeplink URL and the user's JWT, which the browser
 * POSTs to SmartTwin. The other statuses are business outcomes (not exceptions)
 * that the controller translates into a message back on the woonplan page.
 */
class HandoffResult
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_NOT_CONFIGURED = 'not_configured';
    public const STATUS_ADVICE_IN_PROGRESS = 'advice_in_progress';
    public const STATUS_FAILED = 'failed';

    private function __construct(
        public readonly string $status,
        public readonly ?string $url = null,
        public readonly ?string $token = null,
    ) {
    }

    public static function success(string $url, string $token): self
    {
        return new self(self::STATUS_SUCCESS, $url, $token);
    }

    /** The current user has no SmartTwin account yet (no smarttwin_user_id). */
    public static function notConfigured(): self
    {
        return new self(self::STATUS_NOT_CONFIGURED);
    }

    /** Coach flow: another user currently holds the advice session for this dossier. */
    public static function adviceInProgress(): self
    {
        return new self(self::STATUS_ADVICE_IN_PROGRESS);
    }

    /** An API call failed or returned an unexpected/empty payload. */
    public static function failed(): self
    {
        return new self(self::STATUS_FAILED);
    }

    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }
}
