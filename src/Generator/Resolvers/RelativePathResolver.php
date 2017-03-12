<?php

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Utils\FileSystem;
use InvalidArgumentException;

class RelativePathResolver
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileSystem
     */
    private $fileSystem;


    public function __construct(Configuration $configuration, FileSystem $fileSystem)
    {
        $this->configuration = $configuration;
        $this->fileSystem = $fileSystem;
    }


    /**
     * @param string $fileName
     * @return string
     */
    public function getRelativePath($fileName)
    {
        foreach ($this->configuration->getOption(CO::SOURCE) as $directory) {
            if (strpos($fileName, $directory) === 0) {
                return $this->getFileNameWithoutSourcePath($fileName, $directory);
            }
        }

        throw new InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
    }


    /**
     * @param string $fileName
     * @param string $directory
     * @return string
     */
    private function getFileNameWithoutSourcePath($fileName, $directory)
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        $fileName = substr($fileName, strlen($directory) + 1);
        return $this->fileSystem->normalizePath($fileName);
    }
}
