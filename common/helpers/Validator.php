<?php

namespace Common\Helpers;

use App\Routing\Services\Spay;
use App\Services\Request;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

class Validator
{
    /**
     * Метод для проверки DTO полей на не успешность
     *
     * @param string $schemaClass
     * @return boolean
     */
    public static function fails(string $schemaClass): bool
    {
        $schema = new $schemaClass();

        /**
         * @var bool|array<string>
         */
        $spay = (new Spay($schema, Request::getParam()))->validate();

        if (is_array($spay)) {
            return true;
        }
        return false;
    }

    /**
     * Метод для получение полей DTO
     *
     * @param class-string $schemaClass
     * @return array
     */
    public static function fields(string $schemaClass): array
    {
        $reflection = new ReflectionClass($schemaClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $result = [];
        foreach ($properties as $property) {
            /** @var string */
            $name = $property->getName();

            /** @var ReflectionNamedType|null */
            $type = $property->getType();

            /** @var string $typeName */
            $typeName = $type instanceof ReflectionNamedType ? $type->getName() : 'mixed';

            $result[$name] = $typeName;
        }

        return $result;
    }
}
