<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use SplFileInfo;

interface FinderInterface
{
    /**
     * @param string[]|string $source
     * @param array $exclude
     * @param array $extensions
     * @return SplFileInfo[]
     */
    public function find($source, array $exclude = [], array $extensions = ['php']): array;
}
