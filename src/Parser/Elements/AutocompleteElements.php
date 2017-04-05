<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\AutocompleteElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

final class AutocompleteElements implements AutocompleteElementsInterface
{
    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var mixed[]
     */
    private $elements = [];

    public function __construct(ElementStorageInterface $elementStorage)
    {
        $this->elementStorage = $elementStorage;
    }

    /**
     * @return mixed[]
     */
    public function getElements(): array
    {
        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            foreach ($elementList as $element) {
                $this->processElement($element);
            }
        }

        $this->sortElements();

        return $this->elements;
    }

    private function processElement(ElementReflectionInterface $element): void
    {
        if ($element instanceof FunctionReflectionInterface) {
            $this->elements[] = ['f', $element->getPrettyName()];
        } elseif ($element instanceof ClassReflectionInterface) {
            $this->elements[] = ['c', $element->getPrettyName()];

            foreach ($element->getOwnMethods() as $method) {
                $this->elements[] = ['m', $method->getPrettyName()];
            }

            foreach ($element->getOwnProperties() as $property) {
                $this->elements[] = ['p', $property->getPrettyName()];
            }
        }
    }

    private function sortElements(): void
    {
        usort($this->elements, function ($one, $two) {
            return strcasecmp($one[1], $two[1]);
        });
    }
}
