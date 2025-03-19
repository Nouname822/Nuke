<?php

namespace Wheel\Services;

use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Interfaces\CRUD;

/** @psalm-suppress UnusedClass */
class WheelService extends AbstractService implements CRUD
{
    public static function add(): JsonResponse
    {
        return Response::created(['message' => "Сервис WheelService метод add() готов обрабатывать запросы!"]);
    }

    public static function set(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис WheelService метод set() готов обрабатывать запросы!"]);
    }

    public static function del(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис WheelService метод del() готов обрабатывать запросы!"]);
    }

    public static function get(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис WheelService метод get() готов обрабатывать запросы!"]);
    }
}
