<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    public function send($phoneNumber, $message);
}
