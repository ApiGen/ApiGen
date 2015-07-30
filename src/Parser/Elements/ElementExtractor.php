<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementExtractorInterface;
use ApiGen\Contracts\Parser\Elements\ElementFilterInterface;
use ApiGen\Contracts\Parser\Elements\ElementsInterface;
use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

class ElementExtractor implements ElementExtractorInterface
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
     * {@inheritdoc}
     */
    public function extractElementsByAnnotation($annotation, callable $skipClassCallback = null)
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

                if ($skipClassCallback && $skipClassCallback($class)) { // in case when class is prior to it's elements
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


    /**
     * @param array $elements
     * @param string $annotation
     * @param array[] $storage
     * @return array[]
     */
    private function extractByAnnotationAndMerge($elements, $annotation, $storage)
    {
        $foundElements = $this->elementFilter->filterByAnnotation($elements, $annotation);
        return array_merge($storage, array_values($foundElements));
    }


    /**
     * @param array { key => elementList[] } $elements
     * @return array
     */
    private function sortElements($elements)
    {
        foreach ($elements as $key => $elementList) {
            $this->elementSorter->sortElementsByFqn($elementList);
        }
        return $elements;
    }
}
