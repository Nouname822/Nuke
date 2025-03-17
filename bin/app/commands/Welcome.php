<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;
use DateTimeImmutable;

class Welcome extends BaseCommand
{
    /**
     * @var array<string>
     */
    private array $welcome;

    /**
     * @var array<string, array<int|string, class-string|array<string, string>>>
     */
    private array $commands;

    public function __construct()
    {
        parent::__construct();

        /** @var array<string> */
        $this->welcome = require_once Functions::root('@/bin/app/config/welcome.php');

        /** @var array<string, array<int|string, class-string|array<string, string>>> */
        $this->commands = (array)include Functions::root('@/bin/app/config/commands.php');
    }

    public function execute(array $arguments): void
    {
        $this->write($this->welcome['logo'] . "\n", 'white');
        $this->compose("Nuke ", "green");
        $this->compose((new DateTimeImmutable())->format('Y-m-d H:i:s') . PHP_EOL . PHP_EOL, "yellow"); // FIX времени
        $this->write("Использовать:", "yellow");
        $this->write("      php artisan [command] [option]" . PHP_EOL, "white");
        $this->write("Команды:", "yellow");

        foreach ($this->commands as $command => $property) {
            if (!isset($property[1]) || !is_array($property[1])) {
                continue;
            }

            if (isset($property[1]['description'])) {
                $this->compose(sprintf("  %-35s", $command), "green");
                $this->compose(sprintf("  %-35s", $property[1]['description']), "white");
                $this->compose(' |', 'green');

                if (isset($property[1]['use'])) {
                    $this->write(sprintf("  %-35s", $property[1]['use']), "blue");
                }
            }
        }

        $this->write('');
    }
}
