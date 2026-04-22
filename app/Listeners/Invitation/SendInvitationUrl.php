<?php

namespace App\Listeners\Invitation;

use App\Events\Invitation\InvitationCreatedEvent;
use App\Jobs\Sms\SendSmsJob;

/**
 * Dispatch sms service and email service to send invitation url to user
 */
class SendInvitationUrl
{
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(InvitationCreatedEvent $event): void
    {
        $name = $event->userData['name'];
        $phoneNumber = $event->userData['phone_number'];
        $role = $event->userData['role']->value;
        $store = $event->userData['store'];
        $message = "Bonjour $name vous avez été invité sur la boutique $store en tant que $role cliquez sur le lien ci-dessous pour rejoindre la boutique";

        // dispatch job
        SendSmsJob::dispatch($phoneNumber, $message);
    }
}
