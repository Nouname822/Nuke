<?php

namespace Auth\Services;

use App\Services\Request;
use Auth\Models\Admins;
use Auth\Models\JwtBlackList;
use Common\Services\AbstractService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;

/** @psalm-suppress UnusedClass */
class CheckJwtService extends AbstractService
{
    public static function check(): JsonResponse
    {
        $token = Request::getHeader()['Authorization'];

        $payload = JwtService::decode($token);

        if (isset($payload) && !empty($payload) && $payload['iss'] === Request::getHost() && $payload['exp'] > time()) {
            $admin = (new Admins())->findById($payload['user_id'], ['role', 'status']);
            $isValidToken = (new JwtBlackList())->hasToken($token);

            if (isset($admin['data'][0]) && $admin['data'][0]['status'] === 'active' && $isValidToken) {
                $admin = $admin['data'][0];

                if ($admin['role'] === 'super_admin' || $admin['role'] === 'admin' || $admin['role'] === 'moderator') {
                    return Response::success(['message' => 'Вы авторизованы!']);
                }
            }
        }

        return Response::unauthorized(['message' => 'Вы не авторизованы!']);
    }
}
