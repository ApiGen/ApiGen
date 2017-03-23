<?php declare(strict_types=1);

namespace ApiGen\Theme;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Utils\FileSystem;

final class ThemeResources
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

    public function copyToDestination(string $destination): void
    {
        $resources = $this->configuration->getOption(ConfigurationOptions::TEMPLATE)['resources'];
        $this->fileSystem->copy($resources, $destination);
    }
}
