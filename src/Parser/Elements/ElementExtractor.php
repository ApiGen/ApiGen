<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;

final class ElementExtractor implements ElementExtractorInterface
{
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
        ElementFilterInterface $elementFilter,
        ElementStorageInterface $elementStorage,
        ElementSorterInterface $elementSorter
    ) {
        $this->elementFilter = $elementFilter;
        $this->elementStorage = $elementStorage;
        $this->elementSorter = $elementSorter;
    }

    /**
     * @return mixed[]
     */
    public function extractElementsByAnnotation(string $annotation): array
    {
        $elements = [
            // these might be empty, if no classes
            'methods' => [],
            'constants' => [],
            'properties' => [],
        ];

        $elements['functions'] = $this->elementFilter->filterByAnnotation(
            $this->elementStorage->getFunctions(),
            $annotation
        );

        $elements['classes'] = $this->elementFilter->filterByAnnotation(
            $this->elementStorage->getClasses(),
            $annotation
        );

        $elements['interfaces'] = $this->elementFilter->filterByAnnotation(
            $this->elementStorage->getInterfaces(),
            $annotation
        );

        $elements['traits'] = $this->elementFilter->filterByAnnotation(
            $this->elementStorage->getTraits(),
            $annotation
        );

        foreach ($this->elementStorage->getClasses() as $classReflection) {
            $elements['methods'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnMethods(),
                $annotation,
                $elements[Elements::METHODS]
            );
            $elements['constants'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnConstants(),
                $annotation,
                $elements['constants']
            );
            $elements['properties'] = $this->extractByAnnotationAndMerge(
                $classReflection->getOwnProperties(),
                $annotation,
                $elements[Elements::PROPERTIES]
            );
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
