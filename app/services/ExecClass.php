<?php

namespace App\Services;

use App\Errors\NotFound;
use Common\Helpers\JsonResponse;
use ReflectionClass;
use ReflectionMethod;

class ExecClass
{
    private string $class;
    private string $method;
    private array $params;

    public function __construct(string $class, string $method, array $params = [])
    {
        $this->class = $class;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Запуск выполнения метода
     */
    public function init(): ?JsonResponse
    {
        if (!$this->exists()) {
            return (new NotFound())->index();
        }

        $instance = $this->resolveClassInstance();
        $reflection = new ReflectionMethod($instance, $this->method);
        $methodParams = $this->resolveMethodParams($reflection);

        $response = $reflection->invokeArgs($instance, $methodParams);

        return $response instanceof JsonResponse ? $response : null;
    }

    /**
     * Проверка существования класса и метода
     */
    private function exists(): bool
    {
        return class_exists($this->class) && method_exists($this->class, $this->method);
    }

    /**
     * Создание экземпляра класса с учетом аргументов конструктора
     *
     * @return object
     */
    private function resolveClassInstance(): object
    {
        $reflection = new ReflectionClass($this->class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $this->class();
        }

        $constructorParams = $this->resolveMethodParams($constructor);

        return $reflection->newInstanceArgs($constructorParams);
    }

    /**
     * Разрешение параметров метода или конструктора
     *
     * @param ReflectionMethod|\ReflectionFunctionAbstract $reflection
     * @return array
     */
    private function resolveMethodParams($reflection): array
    {
        $methodParams = [];

        foreach ($reflection->getParameters() as $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();

            if ($paramType instanceof \ReflectionNamedType) {
                $paramTypeName = $paramType->getName();

                if (class_exists($paramTypeName)) {
                    $methodParams[$paramName] = new $paramTypeName();
                    continue;
                }

                $methodParams[$paramName] = $this->resolvePrimitiveType($paramTypeName, $paramName);
            } else {
                $methodParams[$paramName] = $this->params[$paramName] ?? null;
            }
        }

        return $methodParams;
    }

    /**
     * Обработка примитивных типов
     *
     * @param string $type
     * @param string $paramName
     * @return mixed
     */
    private function resolvePrimitiveType(string $type, string $paramName): mixed
    {
        return match ($type) {
            'int' => (int)($this->params[$paramName] ?? 0),
            'float' => (float)($this->params[$paramName] ?? 0.0),
            'bool' => (bool)($this->params[$paramName] ?? false),
            'string' => (string)($this->params[$paramName] ?? ''),
            'array' => (array)($this->params[$paramName] ?? []),
            default => null
        };
    }
}
