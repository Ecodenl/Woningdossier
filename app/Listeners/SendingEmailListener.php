<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SendingEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MessageSending $event)
    {
        // WIP mailable detection
        dd($event);
        // If not in production, filter out the e-mail addresses based on a configured whitelist.
        // We filter out local logged emails.
        if (! App::environment(['production', 'prod']) && ! (App::environment('local') && config('mail.default') === 'log')) {
            $tos = array_keys($event->message->getTo());
            $whitelisted = config('hoomdossier.contact.email.whitelist', []);
            $filtered = array_intersect($tos, $whitelisted);
            if (empty($filtered)) {
                Log::warning(__METHOD__ . ': None of the recipients (' . implode(', ', $tos) . ') are in the configured email whitelist (@see hoomdossier.php). Dropping email.');

                $event = null;
                return false;
            } else {
                $event->message->setTo($filtered);
                $ccs = $event->message->getCc();
                if (! empty($ccs)) {
                    $ccs = array_keys($ccs);
                    $filtered = array_intersect($ccs, $whitelisted);
                    $event->message->setCc($filtered);
                }
            }
        }
    }
}
