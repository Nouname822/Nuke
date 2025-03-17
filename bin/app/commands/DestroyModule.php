<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class DestroyModule extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/modules/');
    }

    public function execute(array $arguments): void
    {
        if (isset($arguments[0]) && is_string($arguments[0])) {
            $modulePath = $this->path . $arguments[0];

            $this->moveToBasket($modulePath, $arguments[0]);
            $this->write('Модуль успешно удален!', 'green');
        } else {
            $this->write('Напишите название модуля!', 'red');
        }
        $this->end();
    }
}
