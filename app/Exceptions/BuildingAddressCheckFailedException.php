<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a building address check fails for non-recoverable reasons.
 * This exception is intentionally not reported to Sentry as it represents expected
 * failure scenarios (e.g., address not in BAG, missing municipality mapping).
 */
class BuildingAddressCheckFailedException extends Exception
{
    //
}
