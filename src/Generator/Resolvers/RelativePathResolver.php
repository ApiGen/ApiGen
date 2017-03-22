<?php declare(strict_types=1);

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Utils\FileSystem;
use InvalidArgumentException;

final class RelativePathResolver
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(ConfigurationInterface $configuration, FileSystem $fileSystem)
    {
        $this->configuration = $configuration;
        $this->fileSystem = $fileSystem;
    }

    public function getRelativePath(string $fileName): string
    {
        foreach ($this->configuration->getOption(ConfigurationOptions::SOURCE) as $directory) {
            if (strpos($fileName, $directory) === 0) {
                return $this->getFileNameWithoutSourcePath($fileName, $directory);
            }
        }

        throw new InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
    }

    private function getFileNameWithoutSourcePath(string $fileName, string $directory): string
    {
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);
        $fileName = substr($fileName, strlen($directory) + 1);
        return $this->fileSystem->normalizePath($fileName);
    }
}
