<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface AutocompleteElementsInterface
{
    /**
     * @return mixed[]
     */
    public function getElements(): array;
}
