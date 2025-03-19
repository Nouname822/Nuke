<?php

/** ========================================
 *
 *
 *! Файл: Kernel.php
 ** Директория: app\Kernel.php
 *? Цель: Точка входа в приложение Kernel
 *? Описание: Для запуска и настройки приложение CORS LOGS и т.д
 * Создано: 2025-03-10 01:27:15
 *
 *
============================================ */

namespace App;

use App\Cache\CacheService;
use App\Errors\ServerError;
use App\Routing\Routes;
use Common\Helpers\Functions;
use Common\Helpers\JsonResponse;
use RuntimeException;
use Throwable;

class Kernel
{
    /**
     * Активация логов
     *
     * @return void
     */
    private static function initLogs(): void
    {
        $logPath = Functions::root('@/storage/log');

        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }

        ini_set('log_errors', '1');
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', '0');
        ini_set('error_log', $logPath . '/server.log');
        error_reporting(E_ALL);

        set_exception_handler(function (Throwable $e) {
            error_log("[Exception] " . $e->getMessage() . " в " . $e->getFile() . " на строке " . $e->getLine());
        });

        set_error_handler(function ($severity, $message, $file, $line): bool {
            error_log("[Error] [$severity] $message в $file на строке $line");
            new ServerError();
            return true;
        });

        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error !== null) {
                error_log("[Fatal] {$error['message']} в {$error['file']} на строке {$error['line']}");
                new ServerError();
            }
        });
    }

    /**
     * Для инициализации маршрутизации
     *
     * @return JsonResponse|null
     */
    private static function initRoute(): JsonResponse|null
    {
        /** 
         * @var array{
         *     "main.yml": array{
         *         data: array{
         *             routing: array{
         *                 root_file_path: string
         *             }
         *         }
         *     }
         * }
         */
        $config = CacheService::getOrCache('config', '@/config/app/main.yml');

        if (isset($config['main.yml'])) {
            $filePath = $config['main.yml']['data']['routing']['root_file_path'] ?? null;

            if (!is_string($filePath)) {
                throw new RuntimeException("Ошибка: путь к файлу должен быть строкой, а получено: " . gettype($filePath));
            }

            require_once Functions::root($filePath);
        }
        return (new Routes())->index();
    }

    public static function index(): void
    {
        static::initLogs();
        static::initRoute();
    }
}
