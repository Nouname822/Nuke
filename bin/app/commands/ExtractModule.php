<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class ExtractModule extends BaseCommand
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
            $this->extractFromBasket($arguments[0], $this->path);
            $this->write('Модуль успешно удален!', 'green');
        } else {
            $this->write('Напишите название модуля!', 'red');
        }
        $this->end();
    }
}
