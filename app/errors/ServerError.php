<?php

namespace App\Errors;

use Common\Helpers\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServerError
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Ошибка при обработке запроса!'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
