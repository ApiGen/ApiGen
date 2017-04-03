<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * @todo prepare interface
 */
final class InterfaceReflection
{
    /**
     * @var ReflectionClass
     */
    private $betterClassReflection;

    public function __construct(ReflectionClass $betterClassReflection)
    {
        $this->betterClassReflection = $betterClassReflection;
    }
}
