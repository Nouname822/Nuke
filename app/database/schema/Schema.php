<?php

namespace App\Database\Schema;

use Common\Helpers\Functions;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(Functions::root('@/'));
$dotenv->load();

abstract class Schema
{
    protected string $table;

    protected static function getConnection(): Connection
    {
        return DriverManager::getConnection([
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host'     => $_ENV['DB_HOST'],
            'driver'   => 'pdo_pgsql',
        ]);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    protected static function create(string $tableName, callable $function): void
    {
        $conn = static::getConnection();
        $schemaManager = $conn->createSchemaManager();

        if (!$schemaManager->tablesExist([$tableName])) {
            $schema = new SchemaMethods();
            $function($schema);
            $conn->executeStatement($schema->build($tableName));
        } else {
            echo "\nТаблица $tableName уже существует.\n";
        }
    }

    protected static function update(string $tableName, callable $function): void
    {
        $conn = static::getConnection();

        $schema = new UpdateMethods();
        $function($schema);
        $conn->executeStatement($schema->build($tableName));
    }

    protected static function drop(string $tableName): void
    {
        $conn = static::getConnection();
        $schemaManager = $conn->createSchemaManager();

        if ($schemaManager->tablesExist([$tableName])) {
            $conn->executeStatement("DROP TABLE IF EXISTS $tableName");
        }
    }

    /**
     * Чтобы закатить миграцию в баночку
     *
     * @return void
     */
    abstract public function up(): void;

    /**
     * Чтобы откатить до заводских настроек ежжи
     *
     * @return void
     */
    abstract public function down(): void;
}
