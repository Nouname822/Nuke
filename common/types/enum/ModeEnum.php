<?php

namespace Common\Types\Enum;

enum ModeEnum: string
{
    /**
     * Мод для отображение json ответов
     * ? dev - больше информации отладки
     * ? prod - только то что нужно
     */
    case JsonResponseDisplayMode = 'dev';
}
