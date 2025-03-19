<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use App\Helpers\Logs\Log;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response as HelpersResponse;
use Common\Types\Enum\SqlEnum;
use Symfony\Component\HttpFoundation\Response;

class Add extends CRUD
{
    /**
     * @param array<string, string> $data
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(array $data, null|string $redisKey = null, array $messages = [
        'success' => 'Данные успешно добавлены!',
        'warning' => 'Запись с таким значением уже существует',
        'error' => 'Ошибка при создание данных'
    ]): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->create($data, true);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                if (isset($redisKey)) {
                    RedisService::del($redisKey);
                }
                return HelpersResponse::created(['message' => $messages['success']]);
            } else if ($request['code'] === SqlEnum::DUPLICATE_KEY->value) {
                return HelpersResponse::badRequest(['message' => $messages['warning']]);
            }
        }
        Log::database('[ERROR] ' . $request['code'] . ' ' . $request['message']);
        return HelpersResponse::error(['message' => $messages['error']]);
    }
}
