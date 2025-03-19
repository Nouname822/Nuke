<?php

namespace Card\Controllers;

use Card\Services\CardCrudService;
use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;

/** @psalm-suppress UnusedClass */
class CardController extends AbstractController
{
    public function add(): JsonResponse
    {
        return CardCrudService::add();
    }

    public function set(string $id): JsonResponse
    {
        return CardCrudService::set($id);
    }

    public function del(string $id): JsonResponse
    {
        return CardCrudService::del($id);
    }

    public function recovery(string $id): JsonResponse
    {
        return CardCrudService::recovery($id);
    }

    public function get(string $id): JsonResponse
    {
        return CardCrudService::get($id);
    }
}
