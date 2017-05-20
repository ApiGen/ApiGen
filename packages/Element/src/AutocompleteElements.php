<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\StringRouting\Route\SourceCodeRoute;

final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var SourceCodeRoute
     */
    private $sourceCodeRoute;

    public function __construct(ReflectionStorageInterface $reflectionStorage, SourceCodeRoute $sourceCodeRoute)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->sourceCodeRoute = $sourceCodeRoute;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        $elements = [];
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $path = $this->sourceCodeRoute->constructUrl($functionReflection);
            $elements[$path] = $functionReflection->getName() . '()';
        }

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $path = $this->sourceCodeRoute->constructUrl($classReflection);
            $elements[$path] = $classReflection->getName();
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $path = $this->sourceCodeRoute->constructUrl($interfaceReflection);
            $elements[$path] = $interfaceReflection->getName();
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $path = $this->sourceCodeRoute->constructUrl($traitReflection);
            $elements[$path] = $traitReflection->getName();
        }

        return $elements;
    }
}
