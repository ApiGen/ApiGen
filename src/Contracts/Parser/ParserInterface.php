<?php

namespace ApiGen\Contracts\Parser;

use SplFileInfo;

interface ParserInterface
{

    /**
     * Parser files to element reflections.
     *
     * @param SplFileInfo[] $files
     * @return ParserStorageInterface
     */
    public function parse(array $files);


    /**
     * Get list of error found while parsing the code.
     *
     * @return array
     */
    public function getErrors();
}
