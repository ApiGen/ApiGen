<?php declare(strict_types=1);

namespace ApiGen\Namespaces;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\InterfaceReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class NamespaceStorage
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var NamespaceSorter
     */
    private $namespaceSorter;

    /**
     * @var mixed[]
     */
    private $reflectionsCategorizedToNamespaces = [];

    public function __construct(NamespaceSorter $namespaceSorter, ReflectionStorageInterface $reflectionStorage)
    {
        $this->namespaceSorter = $namespaceSorter;
        $this->reflectionStorage = $reflectionStorage;
    }

    /**
     * @return string[]
     */
    public function getNamespaces(): array
    {
        return array_keys($this->reflectionsCategorizedToNamespaces);
    }

    /**
     * @return ClassReflectionInterface[]|TraitReflectionInterface[]|InterfaceReflectionInterface[]|FunctionReflectionInterface[]
     */
    public function getReflectionsCategorizedToNamespaces(): array
    {
        if ($this->reflectionsCategorizedToNamespaces) {
            return $this->reflectionsCategorizedToNamespaces;
        }

        // @todo: consider value object SingleNamespace
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getClassReflections(), 'classes');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getInterfaceReflections(), 'interfaces');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getTraitReflections(), 'traits');
        $this->categorizeReflectionsToNamespace($this->reflectionStorage->getFunctionReflections(), 'functions');

        return $this->reflectionsCategorizedToNamespaces;
    }

    private function categorizeReflectionsToNamespace(array $reflections, string $type): void
    {
        foreach ($reflections as $reflection) {
            $namespace = $reflection->getPseudoNamespaceName();
            $this->reflectionsCategorizedToNamespaces[$namespace][$type][$reflection->getShortName()] = $reflection;
        }

        $this->reflectionsCategorizedToNamespaces = $this->namespaceSorter->sortNamespaces(
            $this->reflectionsCategorizedToNamespaces
        );
    }
}
