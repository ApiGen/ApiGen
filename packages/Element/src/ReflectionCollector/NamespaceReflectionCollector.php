<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Element\Contract\ReflectionCollector\BasicReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\InNamespaceInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Helper\ReflectionAnalyzer;

final class NamespaceReflectionCollector implements BasicReflectionCollectorInterface
{
    /**
     * @var string
     */
    private $activeNamespace;

    /**
     * @var mixed[]
     */
    private $collectedReflections;

    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        if (! is_a($reflection, InNamespaceInterface::class)) {
            return;
        }

        $reflectionInterface = ReflectionAnalyzer::getReflectionInterfaceFromReflection($reflection);

        /** @var InNamespaceInterface $reflection */
        $namespace = $reflection->getNamespaceName();

        $this->collectedReflections[$reflectionInterface][$namespace][$reflection->getName()] = $reflection;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->collectedReflections[ClassReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->collectedReflections[InterfaceReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->collectedReflections[TraitReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->collectedReflections[FunctionReflectionInterface::class][$this->activeNamespace] ?? [];
    }

    public function hasAnyElements(): bool
    {
        return (bool) count($this->collectedReflections);
    }
}
