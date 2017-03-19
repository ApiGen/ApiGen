<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

final class ElementExtractor implements ElementExtractorInterface
{
    /**
     * @var ElementsInterface
     */
    private $elements;

    /**
     * @var ElementFilterInterface
     */
    private $elementFilter;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var ElementSorterInterface
     */
    private $elementSorter;


    public function __construct(
        ElementsInterface $elements,
        ElementFilterInterface $elementFilter,
        ElementStorageInterface $elementStorage,
        ElementSorterInterface $elementSorter
    ) {
        $this->elements = $elements;
        $this->elementFilter = $elementFilter;
        $this->elementStorage = $elementStorage;
        $this->elementSorter = $elementSorter;
    }


    public function extractElementsByAnnotation(string $annotation): array
    {
        $elements = $this->elements->getEmptyList();
        $elements[Elements::METHODS] = [];
        $elements[Elements::PROPERTIES] = [];

        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            $elementsForMain = $this->elementFilter->filterForMain($elementList);
            $elements[$type] += $this->elementFilter->filterByAnnotation($elementsForMain, $annotation);

            if ($type === Elements::CONSTANTS || $type === Elements::FUNCTIONS) {
                continue;
            }

            foreach ($elementList as $class) {
                /** @var ClassReflectionInterface $class */
                if (! $class->isMain()) {
                    continue;
                }

                $elements[Elements::METHODS] = $this->extractByAnnotationAndMerge(
                    $class->getOwnMethods(),
                    $annotation,
                    $elements[Elements::METHODS]
                );
                $elements[Elements::CONSTANTS] = $this->extractByAnnotationAndMerge(
                    $class->getOwnConstants(),
                    $annotation,
                    $elements[Elements::CONSTANTS]
                );
                $elements[Elements::PROPERTIES] = $this->extractByAnnotationAndMerge(
                    $class->getOwnProperties(),
                    $annotation,
                    $elements[Elements::PROPERTIES]
                );
            }
        }

        return $this->sortElements($elements);
    }


    private function extractByAnnotationAndMerge(array $elements, string $annotation, array $storage): array
    {
        $foundElements = $this->elementFilter->filterByAnnotation($elements, $annotation);

        return array_merge($storage, array_values($foundElements));
    }


    /**
     * @param array { key => elementList[] } $elements
     */
    private function sortElements(array $elements): array
    {
        foreach ($elements as $key => $elementList) {
            $this->elementSorter->sortElementsByFqn($elementList);
        }

        return $elements;
    }
}
