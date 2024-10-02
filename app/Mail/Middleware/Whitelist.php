<?php

namespace App\Mail\Middleware;

use App\Helpers\Arr;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Middleware to filter to/cc addresses (and cancel if to addresses result empty) to prevent mails from test
 * environment to be sent to users (prevent spam/clutter mails).
 * Please note that middleware is only handled if the mail is on the queue, so either use
 * Mail::queue((new Mail)->onQueue())
 * or
 * Mail::send(new Mail) where Mail implements ShouldQueue
 *
 * @class Whitelist
 */
class Whitelist
{
    /**
     * @param \Illuminate\Mail\SendQueuedMailable|? $mail
     */
    public function handle($mail, Closure $next): void
    {
        /** @var \Illuminate\Mail\Mailable $mailable */
        $mailable = $mail->mailable;

        if (config('hoomdossier.contact.email.whitelist_enabled')) {
            $tos = $mailable->to;
            $filtered = $this->filterAddresses($tos);
            if (empty($filtered)) {
                $tos = implode(', ', Arr::pluck($tos, 'address'));
                $class = get_class($mailable);
                Log::warning(__METHOD__ . ": None of the recipients ({$tos}) are in the configured email whitelist (@see hoomdossier.php). Dropping email ({$class}).");
                return;
            } else {
                $mailable->to = $filtered;
                $ccs = $mailable->cc;
                if (! empty($ccs)) {
                    $filtered = $this->filterAddresses($ccs);
                    $mailable->cc = $filtered;
                }
            }
        }

        // $mailable is a reference to the original object, we don't need to (re)set the property on the $mail.
        $next($mail);
    }

    private function filterAddresses(array $addresses): array
    {
        $whitelisted = array_filter(explode(',', config('hoomdossier.contact.email.whitelist', '')));

        $allowed = [];
        foreach ($addresses as $data) {
            if (in_array($data['address'], $whitelisted)) {
                $allowed[] = $data;
            }
        }

        return $allowed;
    }
}