<?php

namespace Common\Helpers;

class Functions
{
    /**
     * Для получение корня проекта
     *
     * @param string $path
     * @return string
     * @psalm-return string
     */
    public static function root(string $path): string
    {
        $root = dirname(__DIR__, 2);

        if (str_starts_with($path, '@/')) {
            return $root . '/' . ltrim(substr($path, 2), '/');
        }

        return $root . '/' . ltrim($path, '/');
    }

    public static function benchmark(callable $callback): void
    {
        $start = microtime(true);
        $callback();
        dd(microtime(true) - $start);
    }

    public static function toSnakeCase(string $text): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $text));
    }
}
