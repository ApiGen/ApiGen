<?php

namespace ApiGen\Utils\Neon;

use ApiGen\Utils\Neon\Exceptions\FileNotReadableException;
use ApiGen\Utils\Neon\Exceptions\MissingFileException;
use Nette\Neon\Neon;

class NeonFile
{

    /**
     * @var string
     */
    private $path;


    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->validatePath($path);
        $this->path = $path;
    }


    /**
     * @param string $path
     * @throws \Exception
     */
    private function validatePath($path)
    {
        if (! file_exists($path)) {
            throw new MissingFileException($path . ' could not be found');
        }

        if (! is_readable($path)) {
            throw new FileNotReadableException($path . ' is not readable.');
        }
    }


    /**
     * @return array
     */
    public function read()
    {
        $json = file_get_contents($this->path);
        return (array) Neon::decode($json);
    }
}
