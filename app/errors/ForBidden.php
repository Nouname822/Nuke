<?php

namespace App\Errors;

use Common\Helpers\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ForBidden
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'warning',
            'code' => Response::HTTP_FORBIDDEN,
            'message' => 'Доступ запрещен!'
        ], Response::HTTP_FORBIDDEN);
    }
}
