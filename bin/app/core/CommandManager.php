<?php

namespace Bin\App\Core;

use Bin\App\Commands\Welcome;
use Common\Helpers\Functions;

class CommandManager
{
    protected array $commands = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    private function registerCommands(): void
    {
        $this->commands = (array)require_once Functions::root('@/bin/app/config/commands.php');
    }

    public function handle(array $argv): void
    {
        /**
         * @var string|null
         */
        $commandName = $argv[1] ?? null;

        if (!isset($this->commands[$commandName])) {
            (new Welcome())->execute($argv);
            return;
        }

        /**
         * @var array
         */
        $commandClass = $this->commands[$commandName];

        if (empty($commandClass)) {
            (new Welcome())->execute($argv);
            return;
        }

        /**
         * @var class-string
         */
        $commandClass = $commandClass[0];

        /** @psalm-suppress MixedMethodCall */
        (new $commandClass())->execute(array_slice($argv, 2));
    }
}
