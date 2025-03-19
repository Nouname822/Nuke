<?php

namespace Common\CRUD\Service;

use App\Database\Model\AbstractModel;
use App\Helpers\Logs\Log;
use Common\CRUD\CRUD;
use Common\Helpers\JsonResponse;
use Common\Helpers\Response;
use Common\Types\Enum\SqlEnum;

class Recovery extends CRUD
{
    /**
     * @param array<string, string> $data
     * @param string $redisKey
     * @return JsonResponse
     */
    public function index(string $id, array $messages = [
        'success' => 'Запись успешно восстановлена!',
        'warning' => 'Запись с таким значением уже существует',
        'error' => 'Ошибка при создание данных'
    ]): JsonResponse
    {
        if (in_array(AbstractModel::class, class_parents($this->model))) {
            $request = $this->model->modify((int)$id, [
                'deleted_at' => null
            ]);
        }

        if (!empty($request) && isset($request['code'])) {
            if ($request['code'] === SqlEnum::OK->value) {
                return Response::success(['message' => $messages['success']]);
            } else if ($request['code'] === SqlEnum::DUPLICATE_KEY->value) {
                return Response::badRequest(['message' => $messages['warning']]);
            }
        }

        Log::database('[ERROR] ' . $request['code'] . ' ' . $request['message']);
        return Response::error(['message' => $messages['error']]);
    }
}
