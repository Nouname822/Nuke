<?php

namespace Card\Services;

use App\Services\Request;
use Card\Dto\CardAddDTO;
use Card\Models\Cards;
use Common\CRUD\Service\Add;
use Common\CRUD\Service\Get;
use Common\CRUD\Service\Recovery;
use Common\CRUD\Service\Set;
use Common\CRUD\Service\SoftDel;
use Common\Helpers\Functions;
use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Helpers\Validator;
use Common\Types\Interfaces\CRUD;
use DateTimeImmutable;

/** @psalm-suppress UnusedClass */
class CardCrudService extends AbstractService implements CRUD
{
    private const REDIS_KEY = 'cards';
    private static Cards|null $model = null;

    private static function init(): void
    {
        if (!static::$model) {
            static::$model = new Cards();
        }
    }

    private static function getBulkData(string $id): array
    {
        $colors = ['#191256', '#E8E6E7'];
        $repeat = 10;
        $data = [];

        for ($i = 1; $i <= $repeat; $i++) {
            $data[] = [
                'prize' => ($i * 100) . 'руб',
                'color' => $colors[$i % 2],
                'wheel_id' => $id
            ];
        }

        return $data;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        self::init();
        return self::$name(...$arguments);
    }











    /**
     * Метод для добавление данных
     *
     * @return JsonResponse
     */
    public static function add(): JsonResponse
    {
        if (Validator::fails(CardAddDTO::class)) {
            return Response::badRequest([
                'message' => 'Заполните все поля!',
                'expected' => Validator::fields(CardAddDTO::class)
            ]);
        }

        $setting = Functions::setting();
        $input = Request::getParam();

        // Создание колеса
        $wheelModel = new $setting['WheelModel']();
        $wheel = $wheelModel->create([
            'name' => $input['name'],
            'attempt' => 0,
            'is_active' => true,
        ], true);

        if ($wheel['code'] !== "200") {
            return Response::error(['message' => 'Ошибка при создании колеса!']);
        }

        $wheelId = $wheel['data'];

        // Массовая вставка элементов колеса
        $wheelItemsModel = new $setting['WheelItemsModel']();
        $wheelItems = $wheelItemsModel->bulkInsert(static::getBulkData($wheelId));

        if ($wheelItems['code'] !== "200") {
            return Response::error(['message' => 'Ошибка при добавлении элементов колеса!']);
        }

        // Создание предложения
        $offerModel = new $setting['OfferModel']();
        $offer = $offerModel->create([
            'name' => $input['name'],
            'link' => '#',
            'title' => 'Заголовок',
            'description' => 'Описание'
        ], true);

        if ($offer['code'] !== "200") {
            return Response::error(['message' => 'Ошибка при создании предложения!']);
        }

        return (new Add(static::$model))->index(
            array_merge($input, ['wheel_id' => $wheelId, 'offer_id' => $offer['data']]),
            static::REDIS_KEY
        );
    }










    /**
     * Метод для обновление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function set(string $id): JsonResponse
    {
        if (Validator::fails(CardAddDTO::class)) {
            return Response::badRequest([
                'message' => 'Заполните все поля!',
                'expected' => Validator::fields(CardAddDTO::class)
            ]);
        }

        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите id!']);
        }

        return (new Set(static::$model))->index((int)$id, array_merge(Request::getParam(), ['updated_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s')]), static::REDIS_KEY);
    }









    /**
     * Метод для удаление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function del(string $id): JsonResponse
    {
        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите id!']);
        }

        return (new SoftDel(static::$model))->index($id);
    }







    /**
     * Метод для получение восстановление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function recovery(string $id): JsonResponse
    {
        if (!static::isValidId($id)) {
            return Response::badRequest(['message' => 'Введите id!']);
        }

        return (new Recovery(static::$model))->index($id);
    }







    /**
     * Метод для получение данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function get(string $id): JsonResponse
    {
        return (new Get(static::$model))->index($id === "all" ? null : [$id]);
    }
}
