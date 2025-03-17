<?php

namespace App\Helpers\Logs;

use App\Cache\CacheService;
use App\Errors\ServerError;
use Common\Helpers\Functions;

class Log
{
    /**
     * Для отправки логов для сервера
     *
     * @param string $message
     * @return void
     */
    public static function error(string $message): void
    {
        error_log($message);
        new ServerError();
    }

    /**
     * Для отправки логов для redis
     *
     * @param string $message
     * @return void
     */
    public static function redis(string $message): void
    {
        /** 
         * @var array{
         *   'main.yml': array{
         *     _timestamp: int,
         *     data: array{
         *       logs: array{
         *         path: string,
         *         fronts: array{
         *           server: string,
         *           database: string,
         *           redis: string
         *         }
         *       },
         *     }
         *   }
         * }
         */
        $config = CacheService::getOrCache('config', '@/config/app/main.yml');

        if (!isset($config['main.yml']['data']['logs']['fronts']['redis'], $config['main.yml']['data']['logs']['path'])) {
            error_log("[Redis Logger Error] Некорректная конфигурация логов.", 3, Functions::root('@/storage/logs/errors.log'));
            return;
        }

        $logFile = Functions::root($config['main.yml']['data']['logs']['path'] . DIRECTORY_SEPARATOR . $config['main.yml']['data']['logs']['fronts']['redis']);

        error_log($message, 3, $logFile);
        new ServerError();
    }

    /**
     * Отправка логов для БД
     *
     * @param string $message
     * @return void
     */
    public static function database(string $message): void
    {
        /** 
         * @var array{
         *   'main.yml': array{
         *     _timestamp: int,
         *     data: array{
         *       logs: array{
         *         path: string,
         *         fronts: array{
         *           server: string,
         *           database: string,
         *           redis: string
         *         }
         *       },
         *     }
         *   }
         * }
         */

        $config = CacheService::getOrCache('config', '@/config/app/main.yml');

        if (!isset($config['main.yml']['data']['logs']['fronts']['database'], $config['main.yml']['data']['logs']['path'])) {
            error_log("[Database Logger Error] Некорректная конфигурация логов.", 3, Functions::root('@/storage/logs/errors.log'));
            return;
        }

        $logFile = Functions::root($config['main.yml']['data']['logs']['path'] . DIRECTORY_SEPARATOR . $config['main.yml']['data']['logs']['fronts']['database']);

        error_log($message, 3, $logFile);
        new ServerError();
    }
}
