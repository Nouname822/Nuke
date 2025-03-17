<?php

/** ========================================
 *
 *
 *! Файл: Password.php
 ** Директория: common\helpers\Password.php
 *? Цель: Класс для комфортной работы с паролями
 * Создано: 2025-02-28 03:48:26
 *
 *
============================================ */

namespace Common\Helpers;

class Password
{
    private static function setting(): array
    {
        return [
            'cost' => 12,
            'memory_cost' => 1 << 17,
            'time_cost' => 4,
            'threads' => 2
        ];
    }

    /**
     * Хеширование пароля
     *
     * @param string $data
     * @return string
     */
    public static function hash(string $data): string
    {
        return password_hash($data, PASSWORD_DEFAULT, self::setting());
    }

    /**
     * Верификация пароля
     *
     * @param string $input
     * @param string $hash
     * @return bool
     */
    public static function verify(string $input, string $hash): bool
    {
        return password_verify($input, $hash);
    }

    /**
     * Проверяет, требует ли хеш обновления
     *
     * @param string $hash
     * @return bool
     */
    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, self::setting());
    }

    /**
     * Получает информацию о хеше
     *
     * @param string $hash
     * @return array
     */
    public static function getInfo(string $hash): array
    {
        return password_get_info($hash);
    }

    /**
     * Генерирует случайный пароль заданной длины
     *
     * @param int $length
     * @return string
     */
    public static function generate(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
    }
}
