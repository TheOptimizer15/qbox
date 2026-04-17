<?php

namespace App\Exceptions;

class ServiceUnavailableException extends ApiException
{
    public function __construct(string $message = "Service unavailable.", \Throwable $previous = null)
    {
        parent::__construct($message, 503, $previous);
    }
}
