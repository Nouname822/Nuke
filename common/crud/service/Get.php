<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Types\Enum\SqlEnum;
use Symfony\Component\HttpFoundation\Response;

class Get extends CRUD
{
    /**
     * @param array<int> $ids
     * @param array<string> $fields
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(array $ids, string $redisKey, array $fields = ['*']): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->findByIds($ids, ['*']);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value && isset($request['data']) && !empty($request['data'])) {
                RedisService::del($redisKey);
                return new JsonResponse([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                    'message' => 'Данные успешно получены',
                    'data' => $request['data']
                ], Response::HTTP_OK);
            }
        }
        return new JsonResponse([
            'status' => 'error',
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Ошибка при получение данных'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
