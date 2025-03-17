<?php

namespace Bin\App\Core;

use Common\Helpers\Functions;

class BasketFileManager
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = Functions::root('@/bin/app/config/basket.php');
    }

    public function read(): array
    {
        /**
         * @var mixed
         */
        $data = include $this->filePath;
        return is_array($data) ? $data : [];
    }

    public function write(array $data): void
    {
        $content = "<?php\n\nreturn " . var_export($data, true) . ";\n";
        file_put_contents($this->filePath, $content);
    }

    public function add(string $key, array $value): void
    {
        $data = $this->read();
        if (!isset($data[$key])) {
            $data[$key] = $value;
            $this->write($data);
        }
    }

    public function remove(string $key): void
    {
        $data = $this->read();

        if (array_key_exists($key, $data)) {
            unset($data[$key]);
            $this->write($data);
        }
    }
}
