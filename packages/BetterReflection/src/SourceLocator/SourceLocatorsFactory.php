<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\SourceLocator;

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
     * @var ProjectVendorSourceLocatorFactory
     */
    private $projectVendorSourceLocatorFactory;

    public function __construct(
        DirectorySourceLocatorFactory $directorySourceLocatorFactory,
        FileSourceLocatorFactory $fileSourceLocatorFactory,
        FallbackSourceLocatorFactory $fallbackSourceLocatorFactory,
        ProjectVendorSourceLocatorFactory $projectVendorSourceLocatorFactory
    ) {
        $this->directorySourceLocatorFactory = $directorySourceLocatorFactory;
        $this->fileSourceLocatorFactory = $fileSourceLocatorFactory;
        $this->fallbackSourceLocatorFactory = $fallbackSourceLocatorFactory;
        $this->projectVendorSourceLocatorFactory = $projectVendorSourceLocatorFactory;
    }

    /**
     * @param string[] $directories
     * @param string[] $files
     */
    public function createFromDirectoriesAndFiles(array $directories, array $files): SourceLocator
    {
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
}
