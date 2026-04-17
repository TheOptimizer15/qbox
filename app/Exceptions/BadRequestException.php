<?php

namespace App\Exceptions;

class BadRequestException extends ApiException
{
    public function __construct(string $message = "Bad Request.", \Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
