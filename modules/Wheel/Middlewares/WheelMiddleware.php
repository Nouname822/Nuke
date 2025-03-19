<?php

namespace Wheel\Middlewares;

use App\Services\Request;
use Common\Helpers\JsonResponse;
use Common\Middleware\AbstractMiddleware;
use Symfony\Component\HttpFoundation\Response;

class WheelMiddleware extends AbstractMiddleware
{
    public function process(Request $request): JsonResponse|null
    {
        $token = $request->getHeader()['Authorization'];

        if(!$token || $token === 'valid-token'){
            return null;
        }

        return new JsonResponse([
            'message' => 'Недостаточно прав доступа'
        ], Response::HTTP_FORBIDDEN);
    }
}