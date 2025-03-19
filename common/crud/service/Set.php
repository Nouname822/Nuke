<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use App\Helpers\Logs\Log;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Enum\SqlEnum;

class Set extends CRUD
{
    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @param string|null $redisKey
     * @param array<string, string> $messages
     * @return JsonResponse
     */
    public function index(
        int $id,
        array $data,
        ?string $redisKey = null,
        array $messages = [
            'success' => 'Данные успешно обновлены!',
            'warning' => 'Запись с таким значением уже существует',
            'error' => 'Ошибка при обновлении данных'
        ]
    ): JsonResponse {
        if (!in_array(AbstractModel::class, class_parents($this->model))) {
            return Response::error(['message' => $messages['error']]);
        }

        $request = $this->model->modify($id, $data);

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                if ($redisKey) {
                    RedisService::del($redisKey);
                }
                return Response::success(['message' => $messages['success']]);
            } elseif ($request['code'] === SqlEnum::DUPLICATE_KEY->value) {
                return Response::badRequest(['message' => $messages['warning']]);
            }
        }

        Log::database('[ERROR] ' . ($request['code'] ?? 'UNKNOWN') . ' ' . ($request['message'] ?? 'No message'));
        return Response::error(['message' => $messages['error']]);
    }
}
