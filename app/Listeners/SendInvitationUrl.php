<?php

namespace App\Listeners;

use App\Events\InvitationCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Dispatch sms service and email service to send invitation url to user
 */
class SendInvitationUrl implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(InvitationCreatedEvent $event): void {}
}
