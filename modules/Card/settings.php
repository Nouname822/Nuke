<?php

/** ========================================
 *
 *
 *! Файл: settings.php
 ** Директория: modules\Card\settings.php
 *? Цель: Описать все настройки модули
 *? Описание: Напишите сюда все настройки модуля чтобы не пришлось редактировать код
 * Создано: 2025-03-19 17:03:35
 *
 *
============================================ */

use Offer\Models\Offer;
use Wheel\Models\Wheel;
use Wheel\Models\WheelItems;

return [
    'WheelModel' => Wheel::class,
    'WheelItemsModel' => WheelItems::class,
    'OfferModel' => Offer::class
];
