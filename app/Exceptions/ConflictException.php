<?php

namespace App\Exceptions;

class ConflictException extends ApiException
{
    public function __construct(string $message = "Conflict detected.", \Throwable $previous = null)
    {
        parent::__construct($message, 409, "CONFLICT", $previous);
    }
}
