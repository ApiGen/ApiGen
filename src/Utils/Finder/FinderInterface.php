<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use SplFileInfo;

interface FinderInterface
{
    /**
     * @param string[] $sources
     * @param string[] $exclude
     * @param string[] $extensions
     * @return SplFileInfo[]
     */
    public function find(array $sources, array $exclude = [], array $extensions = ['php']): array;
}
