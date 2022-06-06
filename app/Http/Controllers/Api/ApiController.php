<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function errorResponse(string $error = null, int $status = 422): JsonResponse
    {
        return response()->json([
            'data' => null,
            'errors' => $error
        ], $status);
    }

    public function okResponse($data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'errors' => null,
        ], $status);
    }
}
