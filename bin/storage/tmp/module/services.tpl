<?php

namespace {{ module_name }}\Services;

use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Interfaces\CRUD;

/** @psalm-suppress UnusedClass */
class {{ name }} extends AbstractService implements CRUD
{
    public static function add(): JsonResponse
    {
        return Response::created(['message' => "Сервис {{ name }} метод add() готов обрабатывать запросы!"]);
    }

    public static function set(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис {{ name }} метод set() готов обрабатывать запросы!"]);
    }

    public static function del(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис {{ name }} метод del() готов обрабатывать запросы!"]);
    }

    public static function recovery(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис {{ name }} метод recovery() готов обрабатывать запросы!"]);
    }

    public static function get(string $id): JsonResponse
    {
        return Response::success(['message' => "Сервис {{ name }} метод get() готов обрабатывать запросы!"]);
    }
}
