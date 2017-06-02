<?php declare(strict_types=1);

namespace ApiGen\Utils;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
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
        $fileName = $this->fileSystem->normalizePath($fileName);
        foreach ($this->configuration->getSource() as $directory) {
            $directory = $this->fileSystem->normalizePath($directory);
            if (strpos($fileName, $directory) === 0) {
                return $this->getFileNameWithoutSourcePath($fileName, $directory);
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Could not determine "%s" relative path',
            $fileName
        ));
    }

    private function getFileNameWithoutSourcePath(string $fileName, string $directory): string
    {
        $directory = $this->fileSystem->normalizePath($directory);
        $fileName = $this->fileSystem->normalizePath($fileName);

        return substr($fileName, strlen($directory) + 1);
    }
}
