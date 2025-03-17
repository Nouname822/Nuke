<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class MakeModel extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/src/models/');
    }

    public function execute(array $arguments): void
    {
        /**
         * @var string
         */
        $name = $arguments[0] ?? 'Default';
        $fullPath = $this->path . $name . '.php';

        $this->createFromTpl('model.tpl', $fullPath, [
            'name' => $name
        ], function () {
            $this->write('Модель успешно создан!', 'green');
        });
        $this->end();
    }
}
