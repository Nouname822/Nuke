<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;
use DateTimeImmutable;

class MakeMigration extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/schema/');
    }

    /**
     * @param array $arguments
     * @return void
     */
    public function execute(array $arguments): void
    {
        /**
         * @var string
         */
        $name = $arguments[0] ?? 'Default';
        $nameHash = (new DateTimeImmutable())->format('Y_m_d_His') . '_create_table_' . Functions::toSnakeCase($name) . '_table';
        $fullPath = $this->path . $nameHash . '.php';

        $this->createFromTpl('migration.tpl', $fullPath, [
            'table' => Functions::toSnakeCase($name)
        ], function () {
            $this->write('Миграция успешно создано!', 'green');
        });

        $isCreateModel = $this->ask('Создать еще модель?', 'yes');

        if ($isCreateModel === "yes" || $isCreateModel === "ye" || $isCreateModel === "y") {
            (new MakeModel())->execute([$name]);
            exit;
        }
        $this->end();
    }
}
