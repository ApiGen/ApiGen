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
    public function getExtensions();


    /**
     * @param string $path
     * @return array
     */
    public function read($path);
}
