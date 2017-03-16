<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\NamespaceUrlFilters;
use ApiGen\Templating\Filters\SourceFilters;

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
     * @var NamespaceUrlFilters
     */
    private $namespaceUrlFilters;


    public function __construct(
        ConfigurationInterface $configuration,
        SourceFilters $sourceFilters,
        ElementUrlFactory $elementUrlFactory,
        NamespaceUrlFilters $namespaceUrlFilters
    ) {
        $this->configuration = $configuration;
        $this->sourceFilters = $sourceFilters;
        $this->elementUrlFactory = $elementUrlFactory;
        $this->namespaceUrlFilters = $namespaceUrlFilters;
    }


    public function getTemplatePath(string $name): string
    {
        $options = $this->configuration->getOptions();
        return $options[CO::TEMPLATE][TCO::TEMPLATES][$name]['template'];
    }


    public function getTemplateFileName(string $name): string
    {
        $options = $this->configuration->getOptions();
        return $this->getDestination() . '/' . $options[CO::TEMPLATE][TCO::TEMPLATES][$name]['filename'];
    }


    public function getTemplatePathForNamespace(string $namespace): string
    {
        return $this->getDestination() . '/' . $this->namespaceUrlFilters->namespaceUrl($namespace);
    }


    public function getTemplatePathForClass(ClassReflectionInterface $element): string
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForClass($element);
    }


    public function getTemplatePathForConstant(ConstantReflectionInterface $element): string
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForConstant($element);
    }


    public function getTemplatePathForFunction(FunctionReflectionInterface $element): string
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForFunction($element);
    }


    public function getTemplatePathForSourceElement(ElementReflectionInterface $element): string
    {
        return $this->getDestination() . '/' . $this->sourceFilters->sourceUrl($element, false);
    }


    public function getTemplatePathForAnnotationGroup(string $element): string
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForAnnotationGroup($element);
    }


    private function getDestination(): string
    {
        return (string) $this->configuration->getOption(CO::DESTINATION);
    }
}
