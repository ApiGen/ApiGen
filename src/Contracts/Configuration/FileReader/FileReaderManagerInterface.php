<?php

namespace ApiGen\Contracts\Configuration\FileReader;

interface FileReaderManagerInterface
{

    /**
     * @param FileReaderInterface $fileReader
     */
    public function addFileReader(FileReaderInterface $fileReader);


    /**
     * @param string $path
     * @return array
     */
    public function read($path);
}
