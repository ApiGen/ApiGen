<?php declare(strict_types=1);

namespace ApiGen\Element\Contract;

interface AutocompleteElementsInterface
{
    /**
     * @return mixed[]
     */
    public function getElements(): array;
}
