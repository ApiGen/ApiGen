<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\AutocompleteElementsInterface;
use ApiGen\Reflection\Contract\ReflectionStorageInterface;

final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ReflectionStorageInterface
     */
    private $reflectionStorage;

    public function __construct(ReflectionStorageInterface $reflectionStorage)
    {
        $this->reflectionStorage = $reflectionStorage;
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
        }

        foreach ($this->reflectionStorage->getInterfaceReflections() as $interfaceReflection) {
            $elements[] = ['i', $interfaceReflection->getName()];
        }

        foreach ($this->reflectionStorage->getTraitReflections() as $traitReflection) {
            $elements[] = ['t', $traitReflection->getName()];
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
