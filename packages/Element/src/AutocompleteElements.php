<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\ReflectionCollector\NamespaceReflectionCollector;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\StringRouting\Route\ReflectionRoute;

final class AutocompleteElements
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    /**
     * @var NamespaceReflectionCollector
     */
    private $namespaceReflectionCollector;

    /**
     * @var NamespaceRoute
     */
    private $namespaceRoute;

    public function __construct(
        ReflectionStorage $reflectionStorage,
        ReflectionRoute $reflectionRoute,
        NamespaceRoute $namespaceRoute,
        NamespaceReflectionCollector $namespaceReflectionCollector
    ) {
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionRoute = $reflectionRoute;
        $this->namespaceReflectionCollector = $namespaceReflectionCollector;
        $this->namespaceRoute = $namespaceRoute;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        $elements = [];

        foreach ($this->namespaceReflectionCollector->getNamespaces() as $namespace) {
            $elements[$namespace] = $this->namespaceRoute->constructUrl($namespace);
        }

        return $this->addReflections($elements);
    }

    /**
     * @param string[] $elements
     * @return string[]
     */
    private function addReflections(array $elements): array
    {
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $name = $functionReflection->getName() . '()';
            $path = $this->reflectionRoute->constructUrl($functionReflection);
            $elements[$name] = $path;
        }

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $elements[$classReflection->getName()] = $this->reflectionRoute->constructUrl($classReflection);
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $elements[$interfaceReflection->getName()] = $this->reflectionRoute->constructUrl($interfaceReflection);
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $elements[$traitReflection->getName()] = $this->reflectionRoute->constructUrl($traitReflection);
        }

        return $elements;
    }
}
