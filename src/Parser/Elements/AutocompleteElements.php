<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\AutocompleteElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;

final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }

    /**
     * @return string[]
     */
    public function getElements(): array
    {
        $elements = [];

        foreach ($this->elementStorage->getFunctions() as $functionReflection) {
            $elements[] = ['f', $functionReflection->getPrettyName()];
        }

        foreach ($this->elementStorage->getClasses() as $classReflection) {
            $elements[] = ['c', $classReflection->getPrettyName()];

            foreach ($classReflection->getOwnMethods() as $methodReflection) {
                $elements[] = ['m', $methodReflection->getPrettyName()];
            }

            foreach ($classReflection->getOwnProperties() as $propertyReflection) {
                $elements[] = ['p', $propertyReflection->getPrettyName()];
            }
        }

        foreach ($this->elementStorage->getInterfaces() as $interfaceReflection) {
            $elements[] = ['c', $interfaceReflection->getPrettyName()];
        }

        foreach ($this->elementStorage->getTraits() as $traitReflection) {
            $elements[] = ['c', $traitReflection->getPrettyName()];
        }

        $elements = $this->sortElements($elements);

        return $elements;
    }

    /**
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
