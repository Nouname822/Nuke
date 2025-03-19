<?php

namespace {{ module_name }}\Services;

use App\Services\Request;
use Common\CRUD\Service\Add;
use Common\CRUD\Service\Get;
use Common\CRUD\Service\Recovery;
use Common\CRUD\Service\Set;
use Common\CRUD\Service\SoftDel;
use Common\Helpers\Response;
use Common\Helpers\Validator;
use Common\Helpers\JsonResponse;
use Common\Services\AbstractService;
use Common\Types\Interfaces\CRUD;
use DateTimeImmutable;

/** @psalm-suppress UnusedClass */
class {{ name }} extends AbstractService implements CRUD
{
    protected static string $redisKey;
    protected static $model;
    protected static string $dtoClass;

    private static function init(): void
    {
        if (!static::$model) {
            static::$model = new static::$model();
        }
    }

    public static function add(): JsonResponse
    {
        static::init();
        if (Validator::fails(static::$dtoClass)) {
            return Response::badRequest([
                'message' => 'Заполните все поля!',
                'expected' => Validator::fields(static::$dtoClass)
            ]);
        }

        return (new Add(static::$model))->index(
            array_merge(Request::getParam(), ['created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')]),
            static::$redisKey
        );
    }

    public static function set(string $id): JsonResponse
    {
        static::init();
        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите корректный id!']);
        }

        return (new Set(static::$model))->index(
            (int)$id,
            array_merge(Request::getParam(), ['updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')]),
            static::$redisKey
        );
    }

    public static function del(string $id): JsonResponse
    {
        static::init();
        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите корректный id!']);
        }

        return (new SoftDel(static::$model))->index($id);
    }

    public static function recovery(string $id): JsonResponse
    {
        static::init();
        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите корректный id!']);
        }

        return (new Recovery(static::$model))->index($id);
    }

    public static function get(string $id): JsonResponse
    {
        static::init();
        return (new Get(static::$model))->index($id === "all" ? null : [$id]);
    }
}
