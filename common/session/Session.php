<?php

namespace Common\Session;


class Session
{

    private const LIFE_TIME = 10;
    // private const LIFE_TIME = (3600 * 24) * 7;

    public function __construct()
    {
        self::start();
    }

    /**
     * Запуск сессии
     *
     * @return void
     */
    private static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', self::LIFE_TIME);
            session_start();
        }
    }

    /**
     * Получение данных сессии
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function get(mixed $key, mixed $default): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Добавление и смена данных сессии
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(mixed $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Проверка на существование данных в сессии
     *
     * @param string $key
     * @return boolean
     */
    public static function has(mixed $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Удаление данных сессии по ключам
     *
     * @param string $key
     * @return void
     */
    public static function remove(mixed $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Очистка данных сессии
     *
     * @return void
     */
    public static function clear(): void
    {
        self::start();
        session_unset();
    }

    /**
     * Завершение работы с сессиями
     *
     * @return void
     */
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }
}
