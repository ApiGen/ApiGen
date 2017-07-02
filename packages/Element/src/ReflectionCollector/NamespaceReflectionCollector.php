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
use Throwable;

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
     * @var string[]
     */
    private $cachedNamespaceKeys = [];

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

        if ($reflectionInterface === ClassReflectionInterface::class) {
            $reflectionInterface = ($reflection->implementsInterface(Throwable::class)) ? 'exception' : 'class';
        }

        $this->collectedReflections[$namespace][$reflectionInterface][$reflection->getName()] = $reflection;
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(string $namespace): array
    {
        return $this->collectedReflections[$namespace]['class'] ?? [];
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getExceptionReflections(string $namespace): array
    {
        return $this->collectedReflections[$namespace]['exception'] ?? [];
    }

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(string $namespace): array
    {
        return $this->collectedReflections[$namespace][InterfaceReflectionInterface::class] ?? [];
    }

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(string $namespace): array
    {
        return $this->collectedReflections[$namespace][TraitReflectionInterface::class] ?? [];
    }

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(string $namespace): array
    {
        return $this->collectedReflections[$namespace][FunctionReflectionInterface::class] ?? [];
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
        if ($this->cachedNamespaceKeys) {
            return $this->cachedNamespaceKeys;
        }

        $namespaceNames = array_keys($this->collectedReflections);
        sort($namespaceNames);

        $this->cachedNamespaceKeys = $namespaceNames;

        return $this->cachedNamespaceKeys;
    }
}
