<?php

namespace Card\Models;

use App\Database\Model\AbstractModel;
use Common\Helpers\Functions;

class Cards extends AbstractModel
{
    private array $setting;

    public function __construct()
    {
        parent::__construct();
        $this->setting = Functions::setting();
    }

    public function wheel(string|int $id): array
    {
        return $this->hasOne($this->setting['WheelModel'], 'id', $id);
    }

    public function offer(string|int $id): array
    {
        return $this->hasOne($this->setting['OfferModel'], 'id', $id);
    }
}
