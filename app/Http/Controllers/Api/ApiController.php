<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected function sendResponse(mixed $data, ?string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        return response()->json($response, $code);
    }

    protected function sendError(string $error, mixed $errorMessages = [], int $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'error'   => $error,
        ];

        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}