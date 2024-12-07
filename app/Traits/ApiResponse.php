<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    public function successResponse($response = null): JsonResponse
    {
        if ($response) {
            return response()->json(
                [

                    'data' => $response
                ],
                Response::HTTP_OK
            );
        }

        return response()->json(
            [
                'data' => [
                    'success' => true,
                ],
            ],
            Response::HTTP_OK
        );
    }

    public function errorResponse($exception, $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return response()->json([
            'data' => [
                'success' => false,
                'message' => $exception->getMessage()
            ]
        ], $statusCode);
    }
}
