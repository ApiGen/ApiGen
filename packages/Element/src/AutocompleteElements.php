<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\Route\SourceCodeRoute;

final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var ReflectionRoute
     */
    private $reflectionRoute;

    public function __construct(ReflectionStorageInterface $reflectionStorage, ReflectionRoute $reflectionRoute)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionRoute = $reflectionRoute;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        $elements = [];
        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $path = $this->reflectionRoute->constructUrl($functionReflection);
            $elements[$path] = $functionReflection->getName() . '()';
        }

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $path = $this->reflectionRoute->constructUrl($classReflection);
            $elements[$path] = $classReflection->getName();
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $path = $this->reflectionRoute->constructUrl($interfaceReflection);
            $elements[$path] = $interfaceReflection->getName();
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $path = $this->reflectionRoute->constructUrl($traitReflection);
            $elements[$path] = $traitReflection->getName();
        }

        return $elements;
    }
}
