<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Element\Naming\ReflectionNaming;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

// @todo: allow service override here
final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    /**
     * @var ReflectionNaming
     */
    private $reflectionNaming;

    public function __construct(ReflectionStorageInterface $reflectionStorage, ReflectionNaming $reflectionNaming)
    {
        $this->reflectionStorage = $reflectionStorage;
        $this->reflectionNaming = $reflectionNaming;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        $elements = [];

        foreach ($this->reflectionStorage->getFunctionReflections() as $functionReflection) {
            $elements[] = ['f', $functionReflection->getName() . '()'];
        }

        foreach ($this->reflectionStorage->getClassReflections() as $classReflection) {
            $elements[] = ['c', $classReflection->getName()];

            foreach ($classReflection->getOwnMethods() as $methodReflection) {
                $elements[] = ['m', $this->reflectionNaming->forMethodReflection($methodReflection)];
            }

            foreach ($classReflection->getOwnProperties() as $propertyReflection) {
                $elements[] = ['p', $this->reflectionNaming->forPropertyReflection($propertyReflection)];
            }
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $elements[] = ['c', $interfaceReflection->getName()];
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $elements[] = ['c', $traitReflection->getName()];
        }

        $elements = $this->sortElements($elements);

        return $elements;
    }

    /**
     * @todo is this needed?
     *
     * @param string[] $elements
     * @return string[]
     */
    private function sortElements(array $elements): array
    {
        usort($elements, function ($one, $two) {
            return strcasecmp($one[1], $two[1]);
        });

        return $elements;
    }
}
