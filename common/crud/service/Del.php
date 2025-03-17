<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Types\Enum\SqlEnum;
use Symfony\Component\HttpFoundation\Response;

class Del extends CRUD
{
    /**
     * @param array<int> $ids
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(array $ids, string $redisKey): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->purge($ids);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                RedisService::del($redisKey);
                return new JsonResponse([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                    'message' => 'Данные успешно удалены'
                ], Response::HTTP_OK);
            }
        }
        return new JsonResponse([
            'status' => 'error',
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Ошибка при удаление данных'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
