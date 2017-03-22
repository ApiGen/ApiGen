<?php declare(strict_types=1);

namespace ApiGen\Utils\Finder;

use SplFileInfo;

interface FinderInterface
{
    /**
     * @param string[]|string $source
     * @param string[] $exclude
     * @param string[] $extensions
     * @return SplFileInfo[]
     */
    public function find($source, array $exclude = [], array $extensions = ['php']): array;
}
