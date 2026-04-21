<?php

namespace App\Services\Sms;

interface SmsService
{
    public function send($phoneNumber, $message);
}
