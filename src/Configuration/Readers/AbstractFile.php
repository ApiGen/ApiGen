<?php declare(strict_types=1);

namespace ApiGen\Configuration\Readers;

use ApiGen\Configuration\Readers\Exceptions\FileNotReadableException;
use ApiGen\Configuration\Readers\Exceptions\MissingFileException;

abstract class AbstractFile
{

    /**
     * @var string
     */
    protected $path;


    public function __construct(string $path)
    {
        $this->validatePath($path);
        $this->path = $path;
    }


    protected function validatePath(string $path): void
    {
        if (! file_exists($path)) {
            throw new MissingFileException($path . ' could not be found');
        }

        if (! is_readable($path)) {
            throw new FileNotReadableException($path . ' is not readable.');
        }
    }
}
