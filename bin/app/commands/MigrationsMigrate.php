<?php

namespace Bin\App\Commands;

use App\Database\Schema\Schema;
use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;
use FilesystemIterator;
use SplFileInfo;

class MigrationsMigrate extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/schema/');
    }

    /**
     * @param array<array-key, mixed> $arguments
     * @return void
     */
    public function run(array $arguments): void
    {
        $filename = $arguments[0] ?? null;
        $action = $arguments[1] ?? 'up';

        if (($action !== 'up' && $action !== 'down')) {
            $this->write("Ошибка: Неверное действие '$action'. Используйте 'up' или 'down'", 'red');
            return;
        }

        if ($filename === null) {
            $this->write("Запуск всех миграций...", 'yellow');
            $this->runAllMigrations($action);
            return;
        }

        if ($filename === '') {
            $this->write("Ошибка: Некорректное имя файла миграции!", 'red');
            return;
        }

        $filename .= '.php';
        $filePath = $this->path . DIRECTORY_SEPARATOR . $filename;

        if (!\is_file($filePath)) {
            $this->write("Ошибка: Файл миграции '$filename' не найден!", 'red');
            return;
        }

        $this->executeMigration($filePath, $action);
    }

    private function runAllMigrations(string $action): void
    {
        $iterator = new FilesystemIterator($this->path, FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $this->executeMigration($file->getPathname(), $action);
        }

        $this->write("Все миграции успешно выполнены!", 'green');
    }

    private function executeMigration(string $filePath, string $action): void
    {
        $filename = basename($filePath);
        $migration = $this->getMigrationInstance($filePath);

        if (!$migration instanceof Schema) {
            $this->write("Ошибка: Невозможно загрузить миграцию '$filename'", 'red');
            return;
        }

        $this->write("Выполнение миграции $filename", 'yellow');

        match ($action) {
            'up' => $migration->up(),
            'down' => $migration->down(),
            default => $this->write("Ошибка: Неизвестная команда '$action'. Используйте 'up' или 'down'", 'red'),
        };

        $this->write("Миграция '$filename' успешно выполнена!", 'green');
    }

    private function getMigrationInstance(string $filePath): ?Schema
    {
        if (!\is_file($filePath) || !\is_readable($filePath)) {
            return null;
        }

        /**
         * @var mixed
         */
        $migration = require $filePath;
        return ($migration instanceof Schema) ? $migration : null;
    }

    /**
     * @param array<array-key, mixed> $arguments
     * @return void
     */
    public function execute(array $arguments): void
    {
        $this->write("Запуск миграции!", 'blue');
        $this->run($arguments);
        $this->end();
    }
}
