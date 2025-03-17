<?php

/** ========================================
 *
 *
 *! Файл: Request.php
 ** Директория: app\service\Request.php
 *? Цель: Получение параметров запроса (Headers, Метод, Путь, Тело, User Agent, Файлы)
 * Создано: 2025-10-28 04:13:16
 *
 *
============================================ */

namespace App\Services;

class Request
{
    /**
     * Параметры запроса
     *
     * @var array Хранит все данные методов GET POST PUT и т.д
     */
    private static array $param;

    /**
     * Хранит шапку запроса
     *
     * @var array Host User-Agent Cookie и т.д
     */
    private static array $headers;

    /**
     * Хранит метод запроса
     *
     * @var string GET POST PUT DELETE PATCH OPTION и т.д
     */
    private static string $method;

    /**
     * Хранит относительный путь
     *
     * @var string /example
     */
    private static string $path;

    /**
     * Хранит полный путь
     *
     * @var string http://127.0.0.1:8080/example
     */
    private static string $uri;

    /**
     * @var string
     */
    private static string $host;

    /**
     * Хранит php://input
     *
     * @var string php://input
     */
    private static string $body;

    /**
     * Хранит User-Agent
     *
     * @var string Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
     */
    private static string $userAgent;

    /**
     * Хранит файлы
     *
     * @var array
     */
    private static array $files;

    /**
     * Инициализация
     *
     * @return void
     */
    public static function initialize(): void
    {
        static::$headers = static::getAllHeaders();

        static::$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        static::$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;

        static::$host = $_SERVER['HTTP_HOST'] ?? '127.0.0.1';

        static::$path = isset($_SERVER['REQUEST_URI']) ? static::getParsedPath(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) : '/';

        static::$body = file_get_contents('php://input');

        static::$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        static::$files = $_FILES;

        static::$param = array_merge($_GET, $_POST, static::parseBody(), static::$files);
    }

    /**
     * Получение шапки
     *
     * @return array
     */
    private static function getAllHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    /**
     * Получение данных всех форматов
     *
     * @return array
     */
    private static function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (empty(static::$body)) {
            return [];
        }

        if (stripos($contentType, 'application/json') !== false) {
            /**
             * @var array<string, mixed>|null
             */
            $data = json_decode(static::$body, true);

            return $data ?? [];
        }

        if (stripos($contentType, 'application/x-www-form-urlencoded') !== false) {
            parse_str(static::$body, $parsed);
            return $parsed;
        }

        return [];
    }


    /**
     * Получение IP-адреса пользователя.
     *
     * @return string IP-адрес (например, "192.168.1.1").
     */
    public static function getIp(): string
    {
        /**
         * @var string|null $forward
         */
        $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;

        $addr = $_SERVER['REMOTE_ADDR'] ?? null;

        if (is_string($forward) && $forward !== '') {
            $ipList = explode(',', $forward);
            return trim($ipList[0]);
        }

        return $addr ?? 'Unknown';
    }

    /**
     * Для получение пути без хоста
     *
     * @param string $path http://127.0.0.1:8080/example
     * @return string /example
     */
    public static function getParsedPath(string $path): string
    {
        return '/' . preg_replace('#/{2,}#', '/', trim($path, '/'));
    }

    /**
     * Получение параметров всех данных запроса
     *
     * @return array ['name' => 'Иванов', 'password' => '123456']
     */
    public static function getParam(): array
    {
        return static::$param;
    }

    /**
     * Получение полной пути
     *
     * @return string http://127.0.0.1:8080/example
     */
    public static function getUri(): string
    {
        return static::$uri;
    }

    /**
     * Для получение шапки
     *
     * @return array ['Host' => '127.0.0.1:8080', 'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36'n и т.д]
     */
    public static function getHeader(): array
    {
        return static::$headers;
    }

    /**
     * Для получение метода запроса
     *
     * @return string GET POST PUT DELETE и т.д
     */
    public static function getMethod(): string
    {
        return static::$method;
    }

    /**
     * Для получение хоста
     *
     * @return string
     */
    public static function getHost(): string
    {
        return static::$host;
    }

    /**
     * Получить относительный путь
     *
     * @return string /example
     */
    public static function getPath(): string
    {
        return static::$path;
    }

    /**
     * Получить User-Agent
     *
     * @return string Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
     */
    public static function getUserAgent(): string
    {
        return static::$userAgent;
    }

    /**
     * Получить файлы
     *
     * @return array $_FILES
     */
    public static function getFiles(): array
    {
        return static::$files;
    }
}
