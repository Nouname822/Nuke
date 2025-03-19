<?php

namespace Offer\Controllers;

use Common\Controller\AbstractController;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class OfferController extends AbstractController
{
    public function add(): JsonResponse
    {
        return Response::created(['message' => "Контроллер OfferController метод add() готов обрабатывать запросы!"]);
    }

    public function set(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер OfferController метод set() готов обрабатывать запросы!"]);
    }

    public function del(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер OfferController метод del() готов обрабатывать запросы!"]);
    }

    public function get(string $id): JsonResponse
    {
        return Response::success(['message' => "Контроллер OfferController метод get() готов обрабатывать запросы!"]);
    }
}
