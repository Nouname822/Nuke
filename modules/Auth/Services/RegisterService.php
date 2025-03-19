<?php

namespace Auth\Services;

use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Auth\Enums\RegisterEnum;
use Auth\Models\Admins;
use Common\CRUD\Service\Add;
use Common\Helpers\Password;

/** @psalm-suppress UnusedClass */
class RegisterService extends AbstractService
{
    private static function getFormattedData(array $data): array
    {
        $role = $data['role'] ?? null;
        $role = RegisterEnum::tryFrom($role);

        $result = [
            'login' => $data['login'] ?? null,
            'email' => $data['email'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'name' => $data['name'] ?? null,
            'role' => $role ? $role->value : null,
            'status' => 'active',
            'password' => $data['password'] ? Password::hash($data['password']) : null,
        ];

        return array_filter($result, fn($value) => !is_null($value));
    }

    public static function register(array $data): JsonResponse
    {
        $data = static::getFormattedData($data);

        return (new Add(new Admins()))->index($data);
    }
}
