<?php

namespace Common\CRUD\Service;

use App\Cache\RedisService;
use App\Database\Model\AbstractModel;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Types\Enum\SqlEnum;
use Symfony\Component\HttpFoundation\Response;

class Add extends CRUD
{
    /**
     * @param array<string, string> $data
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(array $data, string $redisKey): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->create($data, true);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                RedisService::del($redisKey);
                return new JsonResponse([
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Данные успешно добавлены'
                ], 201);
            } else if ($request['code'] === SqlEnum::DUPLICATE_KEY->value) {
                return new JsonResponse([
                    'status' => 'warning',
                    'code' => 400,
                    'message' => 'Запись с таким значением уже существует'
                ], Response::HTTP_BAD_REQUEST);
            }
        }
        return new JsonResponse([
            'status' => 'error',
            'code' => 500,
            'message' => 'Ошибка при создание данных'
        ], 500);
    }
}
