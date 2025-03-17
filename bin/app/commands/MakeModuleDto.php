<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class MakeModuleDto extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/modules/');
    }

    public function execute(array $arguments): void
    {
        /**
         * @var string
         */
        $module = $arguments[0] ?? 'Default';

        /**
         * @var string
         */
        $name = $arguments[1] ?? 'Default';
        $name = $name . 'DTO';
        $fullPath = $this->path . $module . '/Dto/' . $name . '.php';

        $this->createFromTpl('module/dto.tpl', $fullPath, [
            'name' => $name,
            'module_name' => $module
        ], function () {
            $this->write('DTO успешно создан!', 'green');
        });
        $this->end();
    }
}
