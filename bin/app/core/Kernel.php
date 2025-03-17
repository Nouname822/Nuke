<?php

namespace Bin\App\Core;

class Kernel
{
    protected CommandManager $commandManager;

    public function __construct()
    {
        $this->commandManager = new CommandManager();
    }

    public function run(array $argv): void
    {
        $this->commandManager->handle($argv);
    }
}
