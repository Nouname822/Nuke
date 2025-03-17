<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Types\Enum\SqlEnum;
use Symfony\Component\HttpFoundation\Response;

class Set extends CRUD
{
    /**
     * @param int $id
     * @param array $data
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(int $id, array $data, string $redisKey): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->modify($id, $data);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                RedisService::del($redisKey);
                return new JsonResponse([
                    'status' => 'success',
                    'code' => Response::HTTP_OK,
                    'message' => 'Данные успешно обновлены'
                ], Response::HTTP_OK);
            }
        }
        return new JsonResponse([
            'status' => 'error',
            'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Ошибка при обновление данных'
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
