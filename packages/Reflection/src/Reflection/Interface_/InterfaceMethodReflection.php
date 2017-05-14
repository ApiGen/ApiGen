<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class InterfaceMethodReflection implements InterfaceMethodReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionMethod
     */
    private $betterMethodReflection;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(ReflectionMethod $betterMethodReflection)
    {
        $this->betterMethodReflection = $betterMethodReflection;
    }

    public function getName(): string
    {
        return $this->betterMethodReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterMethodReflection->getShortName();
    }

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterMethodReflection->getParameters()
        );
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterMethodReflection->getDeclaringClass()
        );
    }

    public function getDeclaringInterfaceName(): string
    {
        return $this->getDeclaringInterface()
            ->getName();
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
