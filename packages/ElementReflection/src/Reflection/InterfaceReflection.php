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
    private $betterInterfaceReflection;

    public function __construct(ReflectionClass $betterInterfaceReflection)
    {
        $this->betterInterfaceReflection = $betterInterfaceReflection;
    }

    public function getStartLine(): int
    {
        return $this->betterInterfaceReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterInterfaceReflection->getEndLine();
    }

    public function getName(): string
    {
        return $this->betterInterfaceReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterInterfaceReflection->getShortName();
    }

    public function getPrettyName()
    {
        return $this->betterInterfaceReflection->getName() . '()';
    }
}
