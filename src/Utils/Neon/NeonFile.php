<?php declare(strict_types=1);

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


    public function __construct(string $path)
    {
        $this->validatePath($path);
        $this->path = $path;
    }


    private function validatePath(string $path): void
    {
        if (! file_exists($path)) {
            throw new MissingFileException($path . ' could not be found');
        }

        if (! is_readable($path)) {
            throw new FileNotReadableException($path . ' is not readable.');
        }
    }


    public function read(): array
    {
        $json = file_get_contents($this->path);
        return (array) Neon::decode($json);
    }
}
