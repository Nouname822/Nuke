<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;
use DateTimeImmutable;

class MakeModule extends BaseCommand
{
    private string $path;

    /**
     * Можно рядом с "tpl" добавить массив "param" куда можно закинуть дополнительные параметры
     *
     * @var array<string, array<string>>
     */
    private array $folders = [
        'Controllers' => [
            'title' => 'Контроллер',
            'prefix' => 'Controller'
        ],
        'Dto' => [
            'title' => 'DTO',
            'prefix' => 'DTO'
        ],
        'Enums' => [
            'title' => 'ENUM',
            'prefix' => 'Enum'
        ],
        'Interfaces' => [
            'title' => 'Интерфейс',
            'prefix' => 'Interface'
        ],
        'Middlewares' => [
            'title' => 'Middleware',
            'prefix' => 'Middleware'
        ],
        'Models' => [
            'title' => 'Модель',
            'prefix' => ''
        ],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/modules/');
    }

    public function execute(array $arguments): void
    {
        if (!isset($arguments[0])) {
            $this->write('Напишите название модуля!', 'red');
            exit;
        }
        /**
         * @var string
         */
        $name = $arguments[0];
        $fullPath = $this->path . $name;

        foreach ($this->folders as $fileName => $param) {
            mkdir($fullPath . '/' . $fileName, 0777, true);

            $this->createFromTpl('module/' . $fileName . '.tpl', $fullPath . '/' . $fileName . '/' . $name . $param['prefix'] . '.php', [
                'name' => $name . $param['prefix'],
                'module_name' => $name
            ], function () use ($param) {
                $this->write($param['title'] . ' успешно создан!', 'green');
            });
        }

        $this->createFromTpl('module/require.tpl', $fullPath . '/require.php', [
            'module_name' => $name,
            'time' => (new DateTimeImmutable())->format('Y-m-d H:m:i')
        ], function () {
            $this->write('Файл зависимостей успешно создано!', 'green');
        });

        $this->createFromTpl('module/settings.tpl', $fullPath . '/settings.php', [
            'module_name' => $name,
            'time' => (new DateTimeImmutable())->format('Y-m-d H:m:i')
        ], function () {
            $this->write('Файл настроек успешно создано!', 'green');
        });

        $this->createFromTpl('module/routes.tpl', $fullPath . '/routes.php', [], function () {
            $this->write('Файл с маршрутами успешно создано!', 'green');
        });

        touch($fullPath . '/.gitignore');

        $this->end();
    }
}
