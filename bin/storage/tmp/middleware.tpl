<?php

namespace Src\Middleware;

use Common\Helpers\JsonResponse;
use Common\Middleware\AbstractMiddleware;

/** @psalm-suppress UnusedClass */
class {{ name }} extends AbstractMiddleware
{
    public function index()
    {
        return new JsonResponse([
            'message' => '{{ name }} действует'
        ], 200);
    }
}
