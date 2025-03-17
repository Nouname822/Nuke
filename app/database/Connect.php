<?php

namespace App\Database;

use App\Cache\CacheService;
use Common\Helpers\Functions;
use PDO;
use PDOException;

class Connect
{
    private static ?PDO $pdo = null;
    private static array $config = [];
    private string $logFile;

    public function __construct()
    {
        $this->logFile = Functions::root('@/storage/log/database.log');

        if (self::$pdo === null) {
            /**
             * @var array<string, array<string, array>>
             */
            self::$config = CacheService::getOrCache('config', '.env')['.env']['data'] ?? [];

            $dsn = $this->getDsn();

            /**
             * @var string|null
             */
            $user = self::$config['DB_USER'] ?? null;
            /**
             * @var string|null
             */
            $password = self::$config['DB_PASSWORD'] ?? null;

            if ($dsn !== null && is_string($user) && is_string($password)) {
                self::$pdo = $this->connect($dsn, $user, $password);
            }
        }
    }

    private function getDsn(): ?string
    {
        /**
         * @var string|null
         */
        $host = self::$config['DB_HOST'] ?? null;
        /**
         * @var string|null
         */
        $port = self::$config['DB_PORT'] ?? null;
        /**
         * @var string|null
         */
        $dbname = self::$config['DB_NAME'] ?? null;

        if (is_string($host) && is_string($dbname) && is_string($port)) {
            return sprintf("pgsql:host=%s;port=%s;dbname=%s", $host, $port, $dbname);
        }

        return null;
    }

    private function connect(string $dsn, string $user, string $password): ?PDO
    {
        try {
            return new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            $this->logError($e);
            return null;
        }
    }

    private function logError(PDOException $e): void
    {
        $errorMessage = sprintf(
            "[%s] %s in %s on line %d\nStack trace:\n%s\n\n",
            date("Y-m-d H:i:s"),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        error_log($errorMessage, 3, $this->logFile);
    }

    public static function getPdo(): ?PDO
    {
        return self::$pdo;
    }
}
