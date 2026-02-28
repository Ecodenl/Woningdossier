<?php

namespace Tests\Feature\app\Listeners;

use App\Listeners\FilterBlockedEmailDomains;
use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

final class FilterBlockedEmailDomainsTest extends TestCase
{
    private FilterBlockedEmailDomains $listener;

    protected function setUp(): void
    {
        parent::setUp();
        $this->listener = new FilterBlockedEmailDomains();
    }

    public function test_mail_is_cancelled_when_all_recipients_are_blocked(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl');

        $event = $this->createMessageSendingEvent(
            to: [new Address('user@ecogeenmail.nl')],
        );

        $result = $this->listener->handle($event);

        $this->assertFalse($result);
    }

    public function test_blocked_recipient_is_filtered_while_others_remain(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl');

        $event = $this->createMessageSendingEvent(
            to: [
                new Address('user@ecogeenmail.nl'),
                new Address('user@echt.nl'),
            ],
        );

        $result = $this->listener->handle($event);

        $this->assertNull($result);
        $this->assertCount(1, $event->message->getTo());
        $this->assertSame('user@echt.nl', $event->message->getTo()[0]->getAddress());
    }

    public function test_cc_and_bcc_recipients_are_also_filtered(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl');

        $event = $this->createMessageSendingEvent(
            to: [new Address('user@echt.nl')],
            cc: [new Address('cc@ecogeenmail.nl'), new Address('cc@echt.nl')],
            bcc: [new Address('bcc@ecogeenmail.nl')],
        );

        $result = $this->listener->handle($event);

        $this->assertNull($result);
        $this->assertCount(1, $event->message->getTo());
        $this->assertCount(1, $event->message->getCc());
        $this->assertSame('cc@echt.nl', $event->message->getCc()[0]->getAddress());
        $this->assertEmpty($event->message->getBcc());
    }

    public function test_mail_is_cancelled_when_all_to_cc_bcc_are_blocked(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl');

        $event = $this->createMessageSendingEvent(
            to: [new Address('user@ecogeenmail.nl')],
            cc: [new Address('cc@ecogeenmail.nl')],
            bcc: [new Address('bcc@ecogeenmail.nl')],
        );

        $result = $this->listener->handle($event);

        $this->assertFalse($result);
    }

    public function test_mail_passes_through_when_no_blocked_domains_configured(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '');

        $event = $this->createMessageSendingEvent(
            to: [new Address('user@ecogeenmail.nl')],
        );

        $result = $this->listener->handle($event);

        $this->assertNull($result);
        $this->assertCount(1, $event->message->getTo());
    }

    public function test_mail_passes_through_when_recipients_do_not_match(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl');

        $event = $this->createMessageSendingEvent(
            to: [new Address('user@echt.nl')],
        );

        $result = $this->listener->handle($event);

        $this->assertNull($result);
        $this->assertCount(1, $event->message->getTo());
    }

    public function test_multiple_blocked_domains_are_supported(): void
    {
        config()->set('hoomdossier.contact.email.blocked_domains', '@ecogeenmail.nl,@nogeen.nl');

        $event = $this->createMessageSendingEvent(
            to: [
                new Address('a@ecogeenmail.nl'),
                new Address('b@nogeen.nl'),
                new Address('c@echt.nl'),
            ],
        );

        $result = $this->listener->handle($event);

        $this->assertNull($result);
        $this->assertCount(1, $event->message->getTo());
        $this->assertSame('c@echt.nl', $event->message->getTo()[0]->getAddress());
    }

    /**
     * @param  Address[]  $to
     * @param  Address[]  $cc
     * @param  Address[]  $bcc
     */
    private function createMessageSendingEvent(array $to, array $cc = [], array $bcc = []): MessageSending
    {
        $email = (new Email())
            ->subject('Test email')
            ->from(new Address('noreply@hoomdossier.nl'))
            ->to(...$to);

        if (! empty($cc)) {
            $email->cc(...$cc);
        }

        if (! empty($bcc)) {
            $email->bcc(...$bcc);
        }

        return new MessageSending($email);
    }
}
