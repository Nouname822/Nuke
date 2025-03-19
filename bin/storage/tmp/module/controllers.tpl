<?php

namespace {{ module_name }}\Controllers;

use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class {{ name }} extends AbstractController
{
    public function add(): JsonResponse
    {
        return Response::created(['message' => "Контроллер {{ name }} метод add() готов обрабатывать запросы!"]);
    }

    public function set(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер {{ name }} метод set() готов обрабатывать запросы!"]);
    }

    public function del(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер {{ name }} метод del() готов обрабатывать запросы!"]);
    }

    public function get(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер {{ name }} метод get() готов обрабатывать запросы!"]);
    }
}
