<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;
use Common\Helpers\Functions;

class Cache extends BaseCommand
{
    private string $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = Functions::root('@/storage/var/');
    }

    public function execute(array $arguments): void
    {
        $this->deleteFolder($this->path);
        $this->write('Кэш успешно очищен!', 'green');
        $this->end();
    }
}
