<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Configuration\Theme\ThemeConfigOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Utils\FileSystem;
use Exception;

final class TemplateNavigator
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var SourceFilters
     */
    private $sourceFilters;

    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(
        ConfigurationInterface $configuration,
        SourceFilters $sourceFilters,
        ElementUrlFactory $elementUrlFactory,
        FileSystem $fileSystem
    ) {
        $this->configuration = $configuration;
        $this->sourceFilters = $sourceFilters;
        $this->elementUrlFactory = $elementUrlFactory;
        $this->fileSystem = $fileSystem;
    }

    public function getTemplatePath(string $name): string
    {
        $options = $this->configuration->getOptions();
        $templates = $options[ConfigurationOptions::TEMPLATE][ThemeConfigOptions::TEMPLATES];

        if (! isset($templates[$name])) {
            throw new Exception(sprintf(
                'Template for "%s" not found. Available templates: "%s".',
                $name,
                implode('", "', array_keys($templates))
            ));
        }

        return $this->fileSystem->normalizePath($templates[$name]['template']);
    }

    public function getTemplateFileName(string $name): string
    {
        $options = $this->configuration->getOptions();
        return $this->fileSystem->normalizePath($this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $options[ConfigurationOptions::TEMPLATE][ThemeConfigOptions::TEMPLATES][$name]['filename']);
    }

    public function getTemplatePathForClass(ClassReflectionInterface $element): string
    {
        return $this->fileSystem->normalizePath($this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $this->elementUrlFactory->createForClass($element));
    }

    public function getTemplatePathForFunction(FunctionReflectionInterface $element): string
    {
        return $this->fileSystem->normalizePath($this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $this->elementUrlFactory->createForFunction($element));
    }

    public function getTemplatePathForSourceElement(ElementReflectionInterface $element): string
    {
        return $this->fileSystem->normalizePath($this->configuration->getDestination()
            . DIRECTORY_SEPARATOR
            . $this->sourceFilters->sourceUrl($element, false));
    }
}
