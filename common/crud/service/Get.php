<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Enum\SqlEnum;

class Get extends CRUD
{
    /**
     * @param ?array<int> $ids
     * @param string|null $redisKey
     * @param array<string> $fields
     * @param ?int $limit
     * @return JsonResponse
     */
    public function index(?array $ids = null, ?int $limit = null, array $messages = [
        'success' => 'Данные успешно получены',
        'error' => 'Ошибка при получении данных'
    ], array $fields = ['*']): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->findByIds($ids, $fields, $limit);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value && !empty($request['data'])) {
                return Response::success([
                    'message' => $messages['success'],
                    'data' => array_filter($request['data'], fn($item) => is_null($item['deleted_at']))
                ]);
            }
        }
        return Response::error(['message' => $messages['error']]);
    }
}
