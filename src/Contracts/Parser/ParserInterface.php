<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

use SplFileInfo;

interface ParserInterface
{
    /**
     * @param string[] $directories
     */
    public function parseDirectories(array $directories): ParserStorageInterface;

    /**
     * Parser files to element reflections.
     *
     * @param SplFileInfo[] $files
     */
    public function parseFiles(array $files): ParserStorageInterface;
}
