<?php

namespace App\Exceptions;

class MethodNotAllowedException extends ApiException
{
    public function __construct(string $message = "Method not allowed.", \Throwable $previous = null)
    {
        parent::__construct($message, 405, $previous);
    }
}
