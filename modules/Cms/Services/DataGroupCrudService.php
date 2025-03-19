<?php

namespace Cms\Services;

use App\Services\Request;
use Cms\Dto\DataGroupAddDTO;
use Cms\Dto\DataGroupSetDTO;
use Cms\Models\DataGroup;
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
class DataGroupCrudService extends AbstractService implements CRUD
{
    protected static string $redisKey;
    protected static ?DataGroup $model = null;
    protected static ?string $addDTO = null;
    protected static ?string $setDTO = null;

    private static function init(): void
    {
        if (!static::$model) {
            static::$model = new DataGroup();
        }
        if (!static::$addDTO) {
            static::$addDTO = DataGroupAddDTO::class;
        }
        if (!static::$setDTO) {
            static::$setDTO = DataGroupSetDTO::class;
        }
    }

    public static function add(): JsonResponse
    {
        static::init();
        if (Validator::fails(static::$addDTO)) {
            return Response::badRequest([
                'message' => 'Заполните все поля!',
                'expected' => Validator::fields(static::$addDTO)
            ]);
        }

        $input = Request::getParam();

        $data = [
            'name' => $input['name']
        ];

        return (new Add(static::$model))->index(
            $data
        );
    }

    public static function set(string $id): JsonResponse
    {
        static::init();
        if (Validator::fails(static::$setDTO)) {
            return Response::badRequest([
                'message' => 'Заполните все поля!',
                'expected' => Validator::fields(static::$setDTO)
            ]);
        }

        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите корректный id!']);
        }

        $input = Request::getParam();

        $data = [
            'name' => $input['name'],
            'is_active' => (int)$input['isActive']
        ];

        return (new Set(static::$model))->index(
            (int)$id,
            array_merge($data, ['updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')]),
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
        if (!static::isValidId($id)) {
            if ($id !== "all") {
                return Response::badRequest(['message' => 'Введите корректный id!']);
            }
        }

        return (new Get(static::$model))->index($id === "all" ? null : [$id]);
    }
}
