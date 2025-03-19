<?php

namespace Auth\Middlewares;

use App\Services\Request;
use Auth\Models\Admins;
use Auth\Models\JwtBlackList;
use Auth\Services\JwtService;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Middleware\AbstractMiddleware;

class AuthMiddleware extends AbstractMiddleware
{
    public function process(Request $request): JsonResponse|null
    {
        $header = $request->getHeader();

        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];
            $payload = JwtService::decode($token);

            if (isset($payload) && !empty($payload) && $payload['iss'] === Request::getHost() && $payload['exp'] > time()) {
                $admin = (new Admins())->findById($payload['user_id'], ['role', 'status']);
                $isValidToken = (new JwtBlackList())->hasToken($token);

                if (isset($admin['data'][0]) && $admin['data'][0]['status'] === 'active' && $isValidToken) {
                    $admin = $admin['data'][0];

                    if ($admin['role'] === 'super_admin' || $admin['role'] === 'admin' || $admin['role'] === 'moderator') {
                        return null;
                    }
                }
            }
        }

        return Response::unauthorized(['message' => 'Вы не авторизованы!']);
    }
}
