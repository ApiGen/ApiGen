<?php declare(strict_types=1);

namespace ApiGen\Tests;

use ReflectionClass;

final class MethodInvoker
{
    /**
     * @param object $object
     * @param string $method
     * @param array $args
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
