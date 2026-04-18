<?php

namespace App\Exceptions;

class TooManyRequestsException extends ApiException
{
    public function __construct(string $message = "Too many requests.", \Throwable $previous = null)
    {
        parent::__construct($message, 429, "TOO_MANY_REQUESTS", $previous);
    }
}
