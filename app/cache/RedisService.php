<?php

namespace App\Cache;

use App\Helpers\Logs\Log;
use Predis\Client;
use Exception;

class RedisService
{
    private static ?Client $client = null;

    private static function connect(): void
    {
        if (self::$client !== null) {
            if (self::ping() === 'PONG') {
                return;
            }
        }

        try {
            /**
             * @var array
             */
            $config = CacheService::getOrCache('config', '@/config/app/main.yml')['main.yml']['data']['redis'] ?? [];

            if (!empty($config)) {
                self::$client = new Client($config);
            }

            if (self::$client === null || self::ping() !== 'PONG') {
                throw new Exception('Failed to connect to Redis');
            }
            return;
        } catch (Exception $e) {
            Log::redis('[Fatal] Redis connection error: ' . $e->getMessage());
            self::$client = null;
        }
    }

    public static function getClient(): ?Client
    {
        if (self::$client === null) {
            self::connect();
        }
        return self::$client;
    }

    public static function get(string $key): mixed
    {
        $client = self::getClient();
        return $client ? $client->get($key) : null;
    }

    public static function set(string $key, mixed $value): bool
    {
        $client = self::getClient();
        return $client ? (bool)$client->set($key, $value) : false;
    }

    public static function has(string $key): bool
    {
        $client = self::getClient();
        return $client ? $client->exists($key) > 0 : false;
    }

    public static function del(string $key): bool
    {
        $client = self::getClient();
        return $client ? (bool)$client->del($key) : false;
    }

    public static function hset(string $key, array $data): bool
    {
        $client = self::getClient();
        return $client ? (bool)$client->hmset($key, $data) : false;
    }

    public static function hget(string $key, string $field): mixed
    {
        $client = self::getClient();
        return $client ? $client->hget($key, $field) : null;
    }

    public static function rpush(string $key, array $value): bool
    {
        $client = self::getClient();
        return $client ? (bool)$client->rpush($key, $value) : false;
    }

    public static function lrange(string $key, int $start, int $end): array
    {
        $client = self::getClient();
        return $client ? $client->lrange($key, $start, $end) : [];
    }

    public static function expire(string $key, int $seconds): bool
    {
        $client = self::getClient();
        return $client ? (bool)$client->expire($key, $seconds) : false;
    }

    public static function ping(): mixed
    {
        $client = self::getClient();
        return $client ? $client->ping()->getPayload() : 'ERROR';
    }

    private static function getUserId(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return session_id();
    }

    private static function uKey(string $key): ?string
    {
        $userId = self::getUserId();
        return $userId ? "u:{$userId}:{$key}" : null;
    }
}
