<?php

namespace Auth\Services;

use App\Services\Request;
use Common\Helpers\Functions;
use Common\Services\AbstractService;
use Common\Helpers\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/** @psalm-suppress UnusedClass */
class JwtService extends AbstractService
{
    private const ALGO = 'HS256';

    private static null|string $token = null;

    public static function init(): void
    {
        if (!isset(static::$token)) {
            static::$token = Functions::setting()['token'];
        }
    }

    public static function create(array $payload, int $expSeconds = 86400): string
    {
        static::init();
        $payload['exp'] = time() + $expSeconds;
        $payload['iss'] = Request::getHost();
        $payload['iat'] = time();
        return JWT::encode($payload, static::$token, static::ALGO);
    }

    public static function decode(string $jwt): ?array
    {
        try {
            static::init();
            return (array) JWT::decode($jwt, new Key(static::$token, static::ALGO));
        } catch (\Exception $e) {
            return null;
        }
    }
}
