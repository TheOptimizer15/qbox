<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function response(int $status, string $message, mixed $data = []): JsonResponse
    {
        return response()->json(
            [
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $this->normalizeStatus($status)
        )->withHeaders([
            'r-origin' => 'qbox',
            'app-name' => 'qbox-api',
        ]);
    }

    private function normalizeStatus(int $status)
    {
        if ($status < 200 || $status > 299) {
            return 204;
        }
        return $status;
    }
}
