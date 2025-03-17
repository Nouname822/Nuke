<?php

namespace Bin\App\Core;

use Common\Helpers\Functions;
use DateTimeImmutable;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;

abstract class BaseCommand
{
    private float $startTime;
    private string $tplPath;
    protected string $basketPath;
    protected BasketFileManager $basketFileManager;

    public function __construct()
    {
        $this->basketPath = Functions::root('@/storage/basket/');
        $this->startTime = microtime(true);
        $this->basketFileManager = new BasketFileManager();
        $this->tplPath = Functions::root('@/bin/storage/tmp/');
    }

    abstract public function execute(array $arguments): void;

    protected function write(string $message, string $color = 'default'): void
    {
        $colors = [
            'default' => "\033[0m",
            'green'   => "\033[32m",
            'red'     => "\033[31m",
            'yellow'  => "\033[33m",
            'blue'    => "\033[34m",
        ];

        echo ($colors[$color] ?? $colors['default']) . $message . "\033[0m" . PHP_EOL;
    }

    protected function moveToBasket(string $source, string $name): void
    {
        if (!is_dir($this->basketPath)) {
            mkdir($this->basketPath, 0777, true);
        }

        $date = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $baseName = strtolower(basename($source));
        $zipName = (new DateTimeImmutable())->format('Y_m_d_His') . '_' . $baseName . '_' . time() . '.zip';
        $zipFile = $this->basketPath . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE) !== true) {
            $this->write('Ошибка: Невозможно создать архив', 'red');
            return;
        }

        if (is_dir($source)) {
            // Если передана папка — рекурсивно добавляем файлы
            $dir = new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::LEAVES_ONLY);

            foreach ($files as $file) {
                /** @var SplFileInfo $file */
                $filePath = $file->getRealPath();
                if ($filePath === false) {
                    continue;
                }

                $relativePath = substr($filePath, strlen(realpath($source)) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        } elseif (is_file($source)) {
            // Если передан одиночный файл — просто добавляем его в архив
            $zip->addFile($source, $baseName);
        } else {
            $this->write('Ошибка: Указанный путь не существует', 'red');
            return;
        }

        $zip->close();

        exec("rm -rf " . escapeshellarg($source));

        $this->basketFileManager->add($zipName, ['name' => $name, 'file_name' => $zipName, 'created' => $date]);
    }

    protected function extractFromBasket(string $zipFile, string $destination): void
    {
        $zipPath = $this->basketPath . $zipFile . '.zip';

        // Читаем содержимое "корзины"
        $basketContent = $this->basketFileManager->read();
        $zipKey = $zipFile . '.zip';

        // Проверяем, существует ли архив в списке
        if (!isset($basketContent[$zipKey]) || !isset($basketContent[$zipKey]['name'])) {
            $this->write("Архив '$zipFile' не найден в корзине!", 'red');
            return;
        }

        /**
         * @var string
         */
        $name = $basketContent[$zipKey]['name'];

        if (!file_exists($zipPath)) {
            $this->write("Ошибка: Архив '$zipFile' не найден в корзине!", 'red');
            return;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->write("Ошибка: Не удалось открыть архив '$zipFile'", 'red');
            return;
        }

        // Исправленный путь
        $targetPath = rtrim($destination, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

        if (is_dir($targetPath)) {
            $ask = $this->ask("Папка '$name' уже существует! Вы точно хотите заменить её?", 'no');

            if (strtolower($ask) !== "yes" && strtolower($ask) !== "y") {
                $this->write('Операция отменена!', 'red');
                return;
            }
        }

        // Распаковка архива
        $zip->extractTo($targetPath);
        $zip->close();

        $this->write("Архив '$zipFile' успешно извлечен в '$targetPath'", 'green');
    }



    protected function compose(string $message, string $color = 'default'): void
    {
        $colors = [
            'default' => "\033[0m",
            'green'   => "\033[32m",
            'red'     => "\033[31m",
            'yellow'  => "\033[33m",
            'blue'    => "\033[34m",
        ];

        echo ($colors[$color] ?? $colors['default']) . $message . "\033[0m";
    }

    protected function ask(string $question, string $default = ''): string
    {
        $this->write($question . " [{$default}]: ", 'blue');
        $answer = trim(fgets(STDIN));

        return $answer ?: $default;
    }

    /**
     * Undocumented function
     *
     * @param array<array-key, string> $headers
     * @param array<string, array<array-key, string>> $rows
     * @return void
     */
    protected function table(array $headers, array $rows): void
    {
        $columnWidths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            foreach ($row as $key => $cell) {
                $columnWidths[$key] = max($columnWidths[$key], strlen($cell));
            }
        }

        $this->write(str_repeat('-', array_sum($columnWidths) + (count($headers) * 3)), 'yellow');

        $headerLine = '';
        foreach ($headers as $key => $header) {
            $headerLine .= '| ' . str_pad($header, $columnWidths[$key]) . ' ';
        }
        $this->write($headerLine . '|', 'yellow');

        $this->write(str_repeat('-', array_sum($columnWidths) + (count($headers) * 3)), 'yellow');

        foreach ($rows as $row) {
            $line = '';
            foreach ($row as $key => $cell) {
                $line .= '| ' . str_pad($cell, $columnWidths[$key]) . ' ';
            }
            $this->write($line . '|');
        }

        $this->write(str_repeat('-', array_sum($columnWidths) + (count($headers) * 3)), 'yellow');
    }

    protected function logError(string $message): void
    {
        $logPath = Functions::root('@/storage/log/cli.log');
        file_put_contents($logPath, "[" . date('Y-m-d H:i:s') . "] ERROR: $message" . PHP_EOL, FILE_APPEND);
    }

    /**
     * Метод чтобы получить TPL
     *
     * @return array<array-key, string>
     */
    protected function getTplFiles(): array
    {
        /**
         * @var array<array-key, string>
         */
        $tpl = glob($this->tplPath . '*.tpl');

        if ($tpl) {
            return $tpl;
        }
        return [];
    }

    protected function readTpl(string $filename): ?string
    {
        $filepath = $this->tplPath . $filename;
        if (!file_exists($filepath)) {
            $this->write("Ошибка: шаблон '$filename' не найден.", 'red');
            return null;
        }

        return file_get_contents($filepath);
    }

    /**
     * Undocumented function
     *
     * @param string $tplName
     * @param string $FilePath
     * @param array<string, string> $replacements
     * @param callable|null $callback
     * @return void
     */
    protected function createFromTpl(string $tplName, string $FilePath, array $replacements = [], callable $callback = null): void
    {
        if (file_exists($FilePath)) {
            $this->rewrite();
        }

        $tplContent = $this->readTpl($tplName);
        if ($tplContent === null) {
            return;
        }

        foreach ($replacements as $key => $value) {
            $tplContent = str_replace("{{ $key }}", $value, $tplContent);
        }

        file_put_contents($FilePath, $tplContent);
        if (isset($callback)) {
            $callback();
        }
    }

    protected function deleteFolder(string $folderPath): void
    {
        if (!is_dir($folderPath)) {
            return;
        }

        /**
         * @var array<array-key, string>
         */
        $files = array_diff(scandir($folderPath), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

            if (is_dir($filePath)) {
                $this->deleteFolder($filePath);
            } elseif (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                unlink($filePath);
            }
        }

        rmdir($folderPath);
    }

    protected function end(): void
    {
        $executionTime = round(microtime(true) - $this->startTime, 4);
        $this->write("Execution time: {$executionTime} sec", 'green');
    }

    protected function rewrite(): void
    {
        $result = $this->ask('Вы хотите перезаписать файл?', 'no');

        if ($result === "yes" || $result === "y" || $result === "ye") {
            return;
        } else {
            $this->write('Операция отменена!', 'red');
            $this->end();
            exit;
        }
    }
}
