<?php

namespace App\Exceptions;

class InternalServerErrorException extends ApiException
{
    public function __construct(string $message = "Internal server error.", \Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
