<?php

namespace App\Errors;

use Common\Helpers\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class NotFound
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'warning',
            'code' => Response::HTTP_NOT_FOUND,
            'message' => 'Страница не найдена!'
        ], Response::HTTP_NOT_FOUND);
    }
}
