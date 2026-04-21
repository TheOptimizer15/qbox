<?php

namespace App\Jobs\Sms;

use App\Services\Sms\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $phoneNumber, public string $message)
    {
        $this->onQueue('sms');
        $this->afterCommit();
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsProvider): void
    {
        $smsProvider->send($this->phoneNumber, $this->message);
    }
}
