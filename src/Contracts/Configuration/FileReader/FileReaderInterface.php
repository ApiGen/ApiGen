<?php

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
