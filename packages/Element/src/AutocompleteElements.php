<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Reflection\ReflectionStorage;
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

    public function __construct(ReflectionStorage $reflectionStorage, ReflectionRoute $reflectionRoute)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionRoute = $reflectionRoute;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        // @todo: add support for namespace search: type "console" => show Symfony\Console

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
