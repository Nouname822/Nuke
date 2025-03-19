<?php

namespace Wheel\Models;

use App\Database\Model\AbstractModel;

class Wheel extends AbstractModel
{
    public function items(): array
    {
        return $this->hasMany(WheelItems::class, 'wheel_id');
    }
}
