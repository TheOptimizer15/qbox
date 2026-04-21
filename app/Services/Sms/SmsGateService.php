<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;

class SmsGateService implements SmsService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function send($phoneNumber, $message)
    {
        $phone = $this->formatNumber($phoneNumber);
        $username = config('smsgate.username');
        $password = config('smsgate.username');
        $baseUrl = config('smsgate.url');
        $url = "$baseUrl/messages";

        $response = Http::withBasicAuth($username, $password)->withHeaders([])->post($url, [
            'textMessage' => [
                'text' => $message,
            ],
            'phoneNumbers' => [$phoneNumber],
            'withDeliveryReport' => true,
        ]);

        logger()->info("request sent", [$response->json()]);
    }

    private function formatNumber($phoneNumber)
    {
        if (str_starts_with($phoneNumber, '+225') && strlen($phoneNumber) > 10) {
            return $phoneNumber;
        }

        return $phoneNumber;
    }
}
