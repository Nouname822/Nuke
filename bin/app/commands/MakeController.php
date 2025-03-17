<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class MakeController extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/src/controllers/');
    }

    public function execute(array $arguments): void
    {
        /**
         * @var string
         */
        $name = $arguments[0] ?? 'Default';
        $name = $name . 'Controller';
        $fullPath = $this->path . $name . '.php';

        $this->createFromTpl('controller.tpl', $fullPath, [
            'name' => $name
        ], function () {
            $this->write('Контроллер успешно создан!', 'green');
        });
        $this->end();
    }
}
