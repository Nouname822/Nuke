<?php

namespace Auth\Models;

use App\Database\Model\AbstractModel;

class Admins extends AbstractModel
{
    public function findByLogin(string $login): array
    {
        return $this->findOne([['login', '=', $login]], ['id', 'password']);
    }
}
