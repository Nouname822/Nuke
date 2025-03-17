<?php

/** ========================================
 *
 *! Файл: Route.php
 ** Директория: app\routing\auth\Route.php
 *? Цель: Для регистрации маршрутов и сохранения в кэш
 * Создано: 2025-03-09 01:38:11
 *
============================================ */

namespace App\Routing\Auth;

use App\Cache\CacheService;
use App\Helpers\File\FileEditor;
use Common\Helpers\Functions;

class Route
{
    /**
     * @var array<string, array<string, array>>
     */
    private static array $schema = [];

    /** @var array{name?: string, path?: string, middleware?: array<mixed>}|null */
    private static ?array $groupConfig = null;

    private static string $configKey = 'routes_auth_file_last_update';
    private static string $configFile = 'routes';

    /**
     * Функция для генерации схемы маршрута
     *
     * @param string $method
     * @param string $path
     * @param array|callable $action
     * @param array|null $groupConfig
     * @return void
     */
    private static function schema(string $method, string $path, array|callable $action, ?array $groupConfig = null): void
    {
        if (isset($groupConfig['path']) && !empty($groupConfig['path']) && is_string($groupConfig['path'])) {
            $path = $groupConfig['path'] . $path;
        }

        static::$schema[$method][$path] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'name' => $groupConfig['name'] ?? [],
            'middleware' => $groupConfig['middleware'] ?? [],
        ];
    }

    public static function get(string $path, array|callable $action): self
    {
        static::schema('GET', $path, $action, static::$groupConfig);
        return new self();
    }

    public static function post(string $path, array|callable $action): self
    {
        static::schema('POST', $path, $action, static::$groupConfig);
        return new self();
    }

    public static function put(string $path, array|callable $action): self
    {
        static::schema('PUT', $path, $action, static::$groupConfig);
        return new self();
    }

    public static function delete(string $path, array|callable $action): self
    {
        static::schema('DELETE', $path, $action, static::$groupConfig);
        return new self();
    }

    public function name(string $name): self
    {
        $lastMethod = array_key_last(static::$schema);
        if ($lastMethod === null) {
            return $this;
        }

        $lastPath = array_key_last(static::$schema[$lastMethod]);
        if ($lastPath === null) {
            return $this;
        }

        static::$schema[$lastMethod][$lastPath]['name'] = $name;
        return $this;
    }

    public static function group(string $name, string $path, array $middleware, callable $callback): void
    {
        /**
         * @var array{path: string, name: string, middleware: array}
         */
        $previousGroupConfig = static::$groupConfig;

        $newPath = isset($previousGroupConfig['path']) ? rtrim($previousGroupConfig['path'], '/') . '/' . ltrim($path, '/') : $path;

        static::$groupConfig = [
            'name' => isset($previousGroupConfig['name']) ? $previousGroupConfig['name'] . '.' . $name : $name,
            'path' => $newPath,
            'middleware' => array_merge(($previousGroupConfig['middleware'] ?? []), $middleware),
        ];

        $callback();

        static::$groupConfig = $previousGroupConfig;
    }

    public static function loadRoutesFromModules(string $modulesPath): void
    {
        if (!is_dir($modulesPath)) {
            return;
        }

        $moduleDirs = scandir($modulesPath);
        $routesTimestamps = [];

        foreach ($moduleDirs as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $routeFile = $modulesPath . '/' . $module . '/routes.php';

            if (file_exists($routeFile)) {
                $routesTimestamps[$module] = filemtime($routeFile);
            }
        }

        if (CacheService::exists(static::$configFile)) {
            $routesCache = CacheService::get(static::$configFile);

            if (
                isset($routesCache['modules_timestamps']) &&
                $routesCache['modules_timestamps'] === $routesTimestamps
            ) {
                echo "Маршруты не загружаются, кэш совпадает!";
                return;
            }
        }

        foreach ($moduleDirs as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }

            $routeFile = $modulesPath . '/' . $module . '/routes.php';

            if (file_exists($routeFile)) {
                require_once $routeFile;
            }
        }

        CacheService::set(static::$configFile, [
            'modules_timestamps' => $routesTimestamps
        ]);
    }

    public static function auth(): void
    {
        $fileEditor = new FileEditor(Functions::root('@/web/routes.php'));

        if (CacheService::exists(static::$configFile)) {
            $routesCache = CacheService::get(static::$configFile);

            if (
                isset($routesCache[static::$configKey]) &&
                $routesCache[static::$configKey] === $fileEditor->getFileTimestamp()
            ) {
                return;
            }
        }

        if (empty(static::$schema)) {
            throw new \Exception('Маршруты не записаны в static::$schema перед кэшированием!');
        }

        CacheService::set(static::$configFile, [
            static::$configKey => $fileEditor->getFileTimestamp(),
            'main' => static::$schema
        ]);
    }

    public static function register(callable $callback): void
    {
        $callback();
        static::loadRoutesFromModules(Functions::root('@/modules'));
        static::auth();
    }
}
