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
    public function add(): JsonResponse;

    /**
     * Метод для удаление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public function del(string $id): JsonResponse;

    /**
     * Метод для обновление данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public function set(string $id): JsonResponse;

    /**
     * Метод для получение данных
     *
     * @param string $id
     * @return JsonResponse
     */
    public function get(string $id): JsonResponse;
}
