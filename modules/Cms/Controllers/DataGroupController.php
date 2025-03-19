<?php

namespace Cms\Controllers;

use Cms\Services\DataGroupCrudService;
use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;

/** @psalm-suppress UnusedClass */
class DataGroupController extends AbstractController
{
    public function add(): JsonResponse
    {
        return DataGroupCrudService::add();
    }

    public function set(string $id): JsonResponse
    {
        return DataGroupCrudService::set($id);
    }

    public function del(string $id): JsonResponse
    {
        return DataGroupCrudService::del($id);
    }

    public function recovery(string $id): JsonResponse
    {
        return DataGroupCrudService::recovery($id);
    }

    public function get(string $id): JsonResponse
    {
        return DataGroupCrudService::get($id);
    }
}
