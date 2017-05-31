<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use SplFileInfo;

interface FinderInterface
{
    /**
     * @param string[] $sources
     * @return SplFileInfo[]
     */
    public function find(array $sources): array;
}
