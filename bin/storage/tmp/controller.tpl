<?php

namespace Src\Controllers;

use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Interfaces\CRUD;

/** @psalm-suppress UnusedClass */
class {{ name }} extends AbstractController implements CRUD
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
