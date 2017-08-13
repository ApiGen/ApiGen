<?php declare(strict_types=1);

namespace ApiGen\Reflection\Helper;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

final class ReflectionAnalyzer
{
    public static function getReflectionInterfaceFromReflection(AbstractReflectionInterface $reflection): string
    {
        $implementedInterfaces = class_implements($reflection);

        return array_shift($implementedInterfaces);
    }
}
