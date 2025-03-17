<?php

namespace App\Routing\Services;

use ReflectionClass;
use ReflectionNamedType;

class Spay
{
    /**
     * Хранит экземпляр класс для получение свойств
     *
     * @var object class Example { public string $name; public int $age }
     */
    private object $schema;

    /**
     * Хранит данные которые нужно проверить
     *
     * @var array ['name' => 'Иван Иванов', 'age' => 30]
     */
    private array $data;

    public function __construct(object $schema, array $data)
    {
        $this->schema = $schema;
        $this->data = $data;
    }

    /**
     * Метод для валидации данных
     *
     * @return boolean|array
     */
    public function validate(): bool|array
    {
        $reflection = new ReflectionClass($this->schema);
        $properties = $reflection->getProperties();

        $errors = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            $type = $property->getType();

            if (!array_key_exists($name, $this->data)) {
                $errors[$name] = [
                    'expected' => $type instanceof ReflectionNamedType ? $type->getName() : 'unknown',
                    'received' => 'missing'
                ];
                continue;
            }

            if ($type instanceof ReflectionNamedType) {
                $expectedType = $type->getName();
                $isNullable = $type->allowsNull();

                if (!$this->types($this->data[$name], $expectedType, $isNullable)) {
                    $errors[$name] = ['expected' => $expectedType, 'received' => gettype($this->data[$name])];
                }
            }
        }

        return empty($errors) ? true : ['errors' => $errors, 'received_data' => $this->data];
    }

    /**
     * Проверка типов
     * @param mixed $value
     * @param string $type
     * @param bool $nullable
     * @return bool
     */
    private function types(mixed $value, string $type, bool $nullable): bool
    {
        if ($nullable && is_null($value)) {
            return true; // Разрешаем null, если поле nullable
        }

        return match ($type) {
            'int' => is_int($value),
            'string' => is_string($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            'float' => is_float($value),
            default => false,
        };
    }
}
