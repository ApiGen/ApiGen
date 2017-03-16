<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface GroupSorterInterface
{

    /**
     * @return array
     */
    public function sort(array $groups): array;
}
