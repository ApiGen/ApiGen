<?php declare(strict_types=1);

namespace ApiGen\Element\Contract;

interface AutocompleteElementsInterface
{
    // use ReflectionCollector

    /**
     * @return string[][]
     */
    public function getElements(): array;
}
