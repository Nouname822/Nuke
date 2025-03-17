<?php

namespace Bin\App\Commands;

use Bin\App\Core\BaseCommand;

class BasketList extends BaseCommand
{
    public function execute(array $arguments): void
    {
        $data = $this->basketFileManager->read();

        $filteredData = array_filter($data, 'is_array');

        $headers = ['NAME', 'FILE_NAME', 'CREATED'];
        $rows = [];

        foreach ($filteredData as $item) {
            $rows[] = [
                $item['name'] ?? 'N/A',
                $item['file_name'] ?? 'N/A',
                $item['created'] ?? 'N/A',
            ];
        }

        $this->table($headers, $rows);
        $this->end();
    }
}
