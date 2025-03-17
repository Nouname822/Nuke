<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class MakeMiddleware extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/src/middleware/');
    }

    public function execute(array $arguments): void
    {
        /**
         * @var string
         */
        $name = $arguments[0] ?? 'Default';
        $name = $name . 'Middleware';
        $fullPath = $this->path . $name . '.php';

        $this->createFromTpl('middleware.tpl', $fullPath, [
            'name' => $name
        ], function () {
            $this->write('Middleware успешно создан!', 'green');
        });
        $this->end();
    }
}
