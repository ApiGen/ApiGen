<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser;

use SplFileInfo;

interface ParserInterface
{
    /**
     * Parser files to element reflections.
     *
     * @param SplFileInfo[] $files
     */
    public function parse(array $files): ParserStorageInterface;
}
