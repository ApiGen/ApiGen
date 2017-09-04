<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

use ApiGen\Configuration\Configuration;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\SourceLocator;

final class SourceLocatorsFactory
{
    /**
     * @var DirectorySourceLocatorFactory
     */
    private $directorySourceLocatorFactory;

    /**
     * @var FileSourceLocatorFactory
     */
    private $fileSourceLocatorFactory;

    /**
     * @var FallbackSourceLocatorFactory
     */
    private $fallbackSourceLocatorFactory;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        DirectorySourceLocatorFactory $directorySourceLocatorFactory,
        FileSourceLocatorFactory $fileSourceLocatorFactory,
        FallbackSourceLocatorFactory $fallbackSourceLocatorFactory,
        Configuration $configuration
    ) {
        $this->directorySourceLocatorFactory = $directorySourceLocatorFactory;
        $this->fileSourceLocatorFactory = $fileSourceLocatorFactory;
        $this->fallbackSourceLocatorFactory = $fallbackSourceLocatorFactory;
        $this->configuration = $configuration;
    }

    public function create(): SourceLocator
    {
        $sources = $this->configuration->getSource();
        [$files, $directories] = $this->splitSourcesToDirectoriesAndFiles($sources);

        $locators = [];
        if ($directories) {
            $locators[] = $this->directorySourceLocatorFactory->createFromDirectories($directories);
        }

        if ($files) {
            $locators[] = $this->fileSourceLocatorFactory->createFromFiles($files);
        }

        $locators[] = $this->fallbackSourceLocatorFactory->createFromDirecotires($directories);

        return new AggregateSourceLocator($locators);
    }

    /**
     * @param string[] $sources
     * @return string[][]
     */
    private function splitSourcesToDirectoriesAndFiles(array $sources): array
    {
        $files = [];
        $directories = [];

        foreach ($sources as $source) {
            if (is_dir($source)) {
                $directories[] = $source;
            } else {
                $files[] = $source;
            }
        }

        return [$files, $directories];
    }
}
