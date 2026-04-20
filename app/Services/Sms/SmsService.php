<?php

namespace App\Services\SMS;

interface SmsService
{
    public function send($phoneNumber, $message);
}
