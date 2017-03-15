<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests;

use ReflectionClass;

final class MethodInvoker
{
    /**
     * @param mixed $object
     * @param string $method
     * @param mixed[] $args
     * @return mixed
     */
    public static function callMethodOnObject($object, string $method, array $args = [])
    {
        $classReflection = new ReflectionClass($object);
        $methodReflection = $classReflection->getMethod($method);
        $methodReflection->setAccessible(true);
        return $methodReflection->invokeArgs($object, $args);
    }
}
