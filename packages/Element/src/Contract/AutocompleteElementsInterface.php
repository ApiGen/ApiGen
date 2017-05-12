<?php declare(strict_types=1);

namespace ApiGen\Element\Contract;

interface AutocompleteElementsInterface
{
    /**
     * @return string[][]
     */
    public function getElements(): array;
}
