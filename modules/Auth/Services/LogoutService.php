<?php

namespace Auth\Services;

use App\Services\Request;
use Auth\Models\JwtBlackList;
use Common\CRUD\Service\Add;
use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class LogoutService extends AbstractService
{
    public static function logout(): JsonResponse
    {
        return (new Add(new JwtBlackList()))->index([
            'token' => Request::getHeader()['Authorization']
        ], messages: ['success' => 'Успешный выход с учетной записи!', 'warning' => 'Успешный выход с учетной записи!', 'error' => 'Ошибка при выходе из учетной записи!']);
    }
}
