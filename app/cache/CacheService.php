<?php

namespace App\Cache;

use Common\Helpers\Functions;
use Symfony\Component\Yaml\Yaml;

class CacheService
{
    /**
     * @var array<array>
     */
    private static array $cache = [];

    private static function getCachePath(string $name): string
    {
        $directory = Functions::root('@/storage/var/');

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return $directory . $name . '.php';
    }

    public static function exists(string $name): bool
    {
        return isset(self::$cache[$name]) || file_exists(self::getCachePath($name));
    }

    public static function get(string $name): array
    {
        if (isset(self::$cache[$name])) {
            return self::$cache[$name];
        }

        $path = self::getCachePath($name);

        if (!file_exists($path)) {
            return self::$cache[$name] = [];
        }

        /**
         * @var array|null
         */
        $data = include $path;
        return self::$cache[$name] = (is_array($data) ? $data : []);
    }

    public static function set(string $name, array $data, bool $overwrite = true): int|false
    {
        $path = self::getCachePath($name);

        if (!$overwrite && file_exists($path)) {
            /**
             * @var array|null
             */
            $existingData = include $path;

            if (!is_array($existingData)) {
                $existingData = [];
            }

            $data = array_merge($existingData, $data);
        }

        self::$cache[$name] = $data;

        return file_put_contents($path, '<?php return ' . var_export($data, true) . ';');
    }

    /**
     * Получить с и кэшировать файлы
     *
     * @param string $cacheName
     * @param string $path
     * @return array
     */
    public static function getOrCache(string $cacheName, string $path): array
    {
        $fullPath = Functions::root($path);
        $fileEditor = new \App\Helpers\File\FileEditor($fullPath);
        $fileName = basename($path);

        $cachedData = self::get($cacheName);

        if (
            isset($cachedData[$fileName]['_timestamp']) &&
            $cachedData[$fileName]['_timestamp'] === $fileEditor->getFileTimestamp()
        ) {
            return $cachedData;
        }

        $data = self::parseFile($fullPath);

        $cachedData[$fileName] = [
            '_timestamp' => $fileEditor->getFileTimestamp(),
            'data' => $data
        ];

        self::set($cacheName, $cachedData, false);

        return $cachedData;
    }

    /**
     * Метод чтобы парсить файлы
     *
     * @param string $path
     * @return array<string|array-key, mixed>
     * @throws \InvalidArgumentException If the file type is unsupported.
     */
    private static function parseFile(string $path): array
    {
        return match (pathinfo($path, PATHINFO_EXTENSION)) {
            'yaml', 'yml' => (array) (Yaml::parseFile($path) ?? []),
            'env' => self::parseEnvFile($path),
            default => throw new \InvalidArgumentException("Unsupported file type: $path"),
        };
    }

    /**
     * Метод чтобы запарсить .env
     *
     * @param string $path
     * @return array<string, string>
     */
    private static function parseEnvFile(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $data = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                [$key, $value] = $parts;
                $data[trim($key)] = trim($value);
            }
        }

        return $data;
    }
}
