<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;

class FilterBlockedEmailDomains
{
    public function handle(MessageSending $event): ?bool
    {
        $blockedDomains = array_filter(
            explode(',', config('hoomdossier.contact.email.blocked_domains', ''))
        );

        if (empty($blockedDomains)) {
            return null;
        }

        $message = $event->message;

        $filteredTo = $this->filterAddresses($message->getTo(), $blockedDomains);
        $filteredCc = $this->filterAddresses($message->getCc(), $blockedDomains);
        $filteredBcc = $this->filterAddresses($message->getBcc(), $blockedDomains);

        // If no recipients remain at all, cancel the mail.
        if (empty($filteredTo) && empty($filteredCc) && empty($filteredBcc)) {
            Log::info(
                sprintf(
                    '%s: All recipients matched a blocked domain. Dropping email (subject: %s).',
                    __METHOD__,
                    $message->getSubject(),
                )
            );

            return false;
        }

        $message->to(...$filteredTo);
        $message->cc(...$filteredCc);
        $message->bcc(...$filteredBcc);

        return null;
    }

    /**
     * @param  Address[]  $addresses
     * @param  string[]  $blockedDomains
     * @return Address[]
     */
    private function filterAddresses(array $addresses, array $blockedDomains): array
    {
        return array_values(
            array_filter($addresses, function (Address $address) use ($blockedDomains) {
                foreach ($blockedDomains as $domain) {
                    if (str_ends_with($address->getAddress(), $domain)) {
                        Log::info(
                            sprintf(
                                '%s: Filtered recipient %s (matched blocked domain %s).',
                                __METHOD__,
                                $address->getAddress(),
                                $domain,
                            )
                        );

                        return false;
                    }
                }

                return true;
            })
        );
    }
}
