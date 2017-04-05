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

    /**
     * @return mixed[]
     */
    public function extractElementsByAnnotation(string $annotation): array
    {
        $elements = $this->elements->getEmptyList();
        $elements['constants'] = [];
        $elements[Elements::METHODS] = [];
        $elements[Elements::PROPERTIES] = [];

        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            $elements[$type] += $this->elementFilter->filterByAnnotation($elementList, $annotation);

            if ($type === Elements::FUNCTIONS) {
                continue;
            }

            foreach ($elementList as $class) {
                /** @var ClassReflectionInterface $class */
                $elements[Elements::METHODS] = $this->extractByAnnotationAndMerge(
                    $class->getOwnMethods(),
                    $annotation,
                    $elements[Elements::METHODS]
                );
                $elements['constants'] = $this->extractByAnnotationAndMerge(
                    $class->getOwnConstants(),
                    $annotation,
                    $elements['constants']
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

    /**
     * @param mixed[] $elements
     * @param string $annotation
     * @param mixed[] $storage
     * @return mixed[]
     */
    private function extractByAnnotationAndMerge(array $elements, string $annotation, array $storage): array
    {
        $foundElements = $this->elementFilter->filterByAnnotation($elements, $annotation);

        return array_merge($storage, array_values($foundElements));
    }

    /**
     * @param mixed[] $elements
     * @return mixed[]
     */
    private function sortElements(array $elements): array
    {
        foreach ($elements as $key => $elementList) {
            $this->elementSorter->sortElementsByFqn($elementList);
        }

        return $elements;
    }
}
