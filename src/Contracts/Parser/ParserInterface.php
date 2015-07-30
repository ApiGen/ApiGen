<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
