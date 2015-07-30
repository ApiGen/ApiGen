<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Configuration\FileReader;

interface FileReaderInterface
{

    /**
     * @return string[]
     */
    function getExtensions();


    /**
     * @param string $path
     * @return array
     */
    function read($path);
}
