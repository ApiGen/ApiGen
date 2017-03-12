<?php

namespace ApiGen\Contracts\Parser\Elements;

interface GroupSorterInterface
{

    /**
     * @return array
     */
    public function sort(array $groups);
}
