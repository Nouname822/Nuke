<?php

namespace App\Helpers\File;

class FileEditor
{
    /**
     * @var string
     */
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Функция для редактирование файла
     *
     * @param string $content
     * @return void
     */
    public function editFile(string $content): void
    {
        file_put_contents($this->filePath, $content);

        $this->updateFileTimestamp();
    }

    /**
     * Обновить время обновление файла
     *
     * @return void
     */
    private function updateFileTimestamp(): void
    {
        touch($this->filePath, time(), time());
    }

    /**
     * Получить время последнего обновление файла
     *
     * @return integer
     */
    public function getFileTimestamp(): int
    {
        return filemtime($this->filePath);
    }
}
