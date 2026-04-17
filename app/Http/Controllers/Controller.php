<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $data;

    protected $message;

    protected function response($status = 204)
    {
        return response()->json([
            'success' => true,
            'message' => $this->message,
            'data' => $this->data,
        ], $status);
    }
}
