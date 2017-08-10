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
     * @return string[][]
     */
    public function getElements(): array
    {
        $elements = [];

        foreach ($this->namespaceReflectionCollector->getNamespaces() as $namespace) {
            $elements[] = [
                'file' => $this->namespaceRoute->constructUrl($namespace),
                'label' => $namespace,
            ];
        }

        return $this->addReflections($elements);
    }

    /**
     * @param string[][] $elements
     * @return string[][]
     */
    private function addReflections(array $elements): array
    {
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $name = $functionReflection->getName() . '()';
            $path = $this->reflectionRoute->constructUrl($functionReflection);
            $elements[] = [
               'file' => $path,
               'label' => $name,
            ];
        }

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $elements[] = [
                'file' => $this->reflectionRoute->constructUrl($classReflection),
                'label' => $classReflection->getName(),
            ];
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $elements[] = [
                'file' => $this->reflectionRoute->constructUrl($interfaceReflection),
                'label' => $interfaceReflection->getName(),
            ];
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $elements[] = [
                'file' => $this->reflectionRoute->constructUrl($traitReflection),
                'label' => $traitReflection->getName(),
            ];
        }

        return $elements;
    }
}
