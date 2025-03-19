<?php

namespace Wheel\Controllers;

use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class WheelController extends AbstractController
{
    public function add(): JsonResponse
    {
        return Response::created(['message' => "Контроллер WheelController метод add() готов обрабатывать запросы!"]);
    }

    public function set(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер WheelController метод set() готов обрабатывать запросы!"]);
    }

    public function del(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер WheelController метод del() готов обрабатывать запросы!"]);
    }

    public function get(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер WheelController метод get() готов обрабатывать запросы!"]);
    }
}
