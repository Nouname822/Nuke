<?php

namespace Cms\Models;

use App\Database\Model\AbstractModel;

class DataGroup extends AbstractModel
{
    public function data(int|string $id): array
    {
        return $this->hasMany(Data::class, 'group_id', $id);
    }
}
