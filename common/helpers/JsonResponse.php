<?php

namespace Common\Helpers;

use App\Helpers\Logs\Log;
use App\Services\Request;
use Common\Types\Enum\ModeEnum;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use RuntimeException;

class JsonResponse
{
    private array $data;
    private int $code;

    public function __construct(array $data, int $code)
    {
        $this->data = $data;
        $this->code = $code;

        $mode = ModeEnum::JsonResponseDisplayMode->value;

        if (!method_exists($this, $mode)) {
            throw new RuntimeException("[Error] Неизвестный режим JSON ответа: {$mode}");
        }

        $this->$mode();
        exit;
    }

    /**
     * Определяет категорию ответа по HTTP-коду.
     */
    private function getCategory(): string
    {
        return match (true) {
            $this->code >= 100 && $this->code < 200 => 'info',
            $this->code >= 200 && $this->code < 300 => 'success',
            $this->code >= 400 && $this->code < 500 => 'warning',
            $this->code >= 500 && $this->code < 600 => 'error',
            default => throw new RuntimeException("[Error] Недопустимый HTTP-код: {$this->code}"),
        };
    }

    /**
     * Загружает JSON-шаблон из `/templates`.
     */
    private function loadTemplate(): array
    {
        $category = $this->getCategory();
        $filePath = Functions::root("/templates/{$category}/{$this->code}.json");

        if (!file_exists($filePath)) {
            Log::error("[Error] JSON шаблон {$category}/{$this->code}.json не найден!");
            return [];
        }

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            Log::error("[Error] Ошибка чтения файла {$filePath}");
            return [];
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : [];
        } catch (\JsonException $e) {
            Log::error("[Error] Ошибка парсинга JSON: {$e->getMessage()} в файле {$filePath}");
            return [];
        }
    }


    /**
     * Генерирует JSON-ответ с шаблоном.
     */
    private function generateResponse(): array
    {
        $templateData = $this->loadTemplate();

        if (isset($this->data['message']) && is_string($this->data['message'])) {
            $templateData['message'] = $this->data['message'];
        }

        return array_merge($templateData, $this->data);
    }

    /**
     * Для отправки JSON-ответов в режиме разработки.
     */
    private function dev(): void
    {
        $end_time = microtime(true);

        if (is_float($end_time)) {
            /**
             * @var float
             */
            $execution_time = ($end_time - START_TIME) * 1000;
        } else {
            $execution_time = 0;
        }

        (new HttpFoundationJsonResponse([
            ...$this->generateResponse(),
            'header' => Request::getHeader(),
            'path' => Request::getPath(),
            'method' => Request::getMethod(),
            'ip' => Request::getIp(),
            'params' => Request::getParam(),
            'timestamp' => (new DateTimeImmutable())->format(DateTime::ATOM),
            'processing_time' => $execution_time . 'мс'
        ], $this->code))->send();
    }

    /**
     * Для отправки данных в режиме продакшн.
     */
    private function prod(): void
    {
        (new HttpFoundationJsonResponse($this->generateResponse(), $this->code))->send();
    }
}
