<?php declare(strict_types=1);

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Utils\FileSystem;
use InvalidArgumentException;
use Nette\Utils\Strings;

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
        $sources = $this->configuration->getOption(ConfigurationOptions::SOURCE);
        foreach ($sources as $directory) {
            $directory = $this->fileSystem->normalizePath($directory);

            if (Strings::startsWith($fileName, $directory)) {
                return $this->getFileNameWithoutSourcePath($fileName, $directory);
            }
        }

        throw new InvalidArgumentException(sprintf(
            'Could not determine relative path for "%s". Looked at available sources: %s.',
            $fileName,
            implode(', ', $sources)
        ));
    }

    private function getFileNameWithoutSourcePath(string $fileName, string $directory): string
    {
        $directory = $this->fileSystem->normalizePath($directory);
        $fileName = $this->fileSystem->normalizePath($fileName);

        return substr($fileName, strlen($directory) + 1);
    }
}
