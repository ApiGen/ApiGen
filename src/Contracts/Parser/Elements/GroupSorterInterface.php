<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

interface GroupSorterInterface
{
    public function sort(array $groups): array;
}
