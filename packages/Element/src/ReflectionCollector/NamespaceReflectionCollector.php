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
    public const NO_NAMESPACE = 'none';

    /**
     * @var mixed[]
     */
    private $collectedReflections = [];

    /**
     * @var string
     */
    private $activeNamespace;

    /**
     * @param InNamespaceInterface|AbstractReflectionInterface $reflection
     */
    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        if (! is_a($reflection, InNamespaceInterface::class)) {
            return;
        }

        $reflectionInterface = ReflectionAnalyzer::getReflectionInterfaceFromReflection($reflection);
        $namespace = $reflection->getNamespaceName() ?: self::NO_NAMESPACE;

        $this->collectedReflections[$namespace][$reflectionInterface][$reflection->getName()] = $reflection;
    }

    public function setActiveNamespace(string $activeNamespace): void
    {
        $this->activeNamespace = $activeNamespace;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array
    {
        return $this->collectedReflections[$this->activeNamespace][ClassReflectionInterface::class] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array
    {
        return $this->collectedReflections[$this->activeNamespace][InterfaceReflectionInterface::class] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array
    {
        return $this->collectedReflections[$this->activeNamespace][TraitReflectionInterface::class] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array
    {
        return $this->collectedReflections[$this->activeNamespace][FunctionReflectionInterface::class] ?? [];
    }

    public function hasAnyElements(): bool
    {
        return (bool) count($this->collectedReflections);
    }

    /**
     * @return string[]
     */
    public function getNamespaces(): array
    {
        // todo: complete all parents!
        $namespaceNames = array_keys($this->collectedReflections);
        sort($namespaceNames);

        return $namespaceNames;
    }
}
