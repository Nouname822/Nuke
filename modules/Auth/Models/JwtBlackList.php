<?php

namespace Auth\Models;

use App\Database\Model\AbstractModel;

class JwtBlackList extends AbstractModel
{
    public function hasToken(string $token): bool
    {
        $token = $this->findOne([['token', '=', $token]], ['id']);

        return empty($token['data']) ? true : false;
    }
}
