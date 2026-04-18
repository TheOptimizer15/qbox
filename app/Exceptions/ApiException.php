<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class ApiException extends Exception
{
    protected int $statusCode;
    protected string $messageCode;

    public function __construct(string $message = "", int $statusCode = 500, string $messageCode ="INTERNAL_SERVER_ERROR", \Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->messageCode = $messageCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessageCode(): string
    {
        return $this->messageCode;
    }
}
