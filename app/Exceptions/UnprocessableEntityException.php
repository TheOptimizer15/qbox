<?php

namespace App\Exceptions;

class UnprocessableEntityException extends ApiException
{
    public function __construct(string $message = "Unprocessable entity.", \Throwable $previous = null)
    {
        parent::__construct($message, 422, "UNPROCESSABLE_ENTITY", $previous);
    }
}
