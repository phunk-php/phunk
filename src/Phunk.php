<?php

namespace Phunk;

class Phunk
{
    public static function compose(array $methods, mixed $initialValue = null): mixed
    {
        return array_reduce($methods, function(mixed $carry, callable $callable) {
            return $callable($carry);
        }, $initialValue);
    }

    public static function fn(callable $method, ...$params): callable
    {
        return function($data) use ($method, $params): mixed {
            $params[] = $data;
            return call_user_func_array($method, $params);
        };
    }

    public static function map(callable $method): callable
    {
        return function(array $data) use ($method): array {
            return array_map($method, $data);
        };
    }

    public static function try(array $methods, mixed $initialValue = null): mixed
    {
        return array_reduce($methods, function(mixed $carry, callable $callable) {
            try {
                return $callable($carry);
            } catch (\Exception $e) {
                return $carry;
            }
        }, $initialValue);
    }

}
