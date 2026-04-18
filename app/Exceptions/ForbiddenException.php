<?php

namespace App\Exceptions;

class ForbiddenException extends ApiException
{
    public function __construct(string $message = "Action forbidden.", \Throwable $previous = null)
    {
        parent::__construct($message, 403, "FORBIDDEN", $previous);
    }
}
