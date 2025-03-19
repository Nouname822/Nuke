<?php

namespace Auth\Services;

use Auth\Models\Admins;
use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Password;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class LoginService extends AbstractService
{
    public static function login(array $data): JsonResponse
    {
        $admin = (new Admins())->findByLogin($data['login'] ?? '');

        if (isset($admin['data']) && isset($admin['data'][0]['id'])) {
            $admin = $admin['data'][0];

            if (Password::verify($data['password'], $admin['password'])) {
                $token = JwtService::create(['user_id' => $admin['id']]);

                return Response::success(['message' => 'Успешное авторизация', 'token' => $token]);
            }
        }

        return Response::badRequest(['message' => 'Неверный логин или пароль!']);
    }
}
