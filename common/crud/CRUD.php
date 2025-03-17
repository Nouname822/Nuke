<?php

namespace Common\CRUD;

use App\Database\Model\AbstractModel;

abstract class CRUD
{
    /**
     * @var AbstractModel
     */
    protected AbstractModel $model;

    protected const REDIS_CRUD_LIFE_TIME = ((60 * 60) * 24) * 7;

    public function __construct(object $model)
    {
        $this->model = $model;
    }
}
