<?php

namespace Common\Types\Interfaces;

use Common\Helpers\JsonResponse;

interface CRUD
{
    /**
     * Метод для добавление данных
     *
     * @return JsonResponse
     */
    public static function add(): JsonResponse;

    /**
     * Метод для удаление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function del(string $id): JsonResponse;

    /**
     * Метод для обновление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function set(string $id): JsonResponse;

    /**
     * Метод для получение данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function get(string $id): JsonResponse;

    /**
     * Метод для получение восстановление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public static function recovery(string $id): JsonResponse;
}
