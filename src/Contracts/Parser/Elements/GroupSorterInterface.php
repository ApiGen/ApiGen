<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface GroupSorterInterface
{
    /**
     * @param mixed[] $groups
     * @return mixed[]
     */
    public function sort(array $groups): array;
}
