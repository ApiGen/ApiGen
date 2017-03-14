<?php

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\NamespaceUrlFilters;
use ApiGen\Templating\Filters\SourceFilters;

class TemplateNavigator
{

    /**
     * @var Configuration
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
        Configuration $configuration,
        SourceFilters $sourceFilters,
        ElementUrlFactory $elementUrlFactory,
        NamespaceUrlFilters $namespaceUrlFilters
    ) {
        $this->configuration = $configuration;
        $this->sourceFilters = $sourceFilters;
        $this->elementUrlFactory = $elementUrlFactory;
        $this->namespaceUrlFilters = $namespaceUrlFilters;
    }


    /**
     * @param string $name
     * @return string
     */
    public function getTemplatePath($name)
    {
        $options = $this->configuration->getOptions();
        return $options[CO::TEMPLATE][TCO::TEMPLATES][$name]['template'];
    }


    /**
     * @param string $name
     * @return string
     */
    public function getTemplateFileName($name)
    {
        $options = $this->configuration->getOptions();
        return $this->getDestination() . '/' . $options[CO::TEMPLATE][TCO::TEMPLATES][$name]['filename'];
    }


    /**
     * @param string $namespace
     * @return string
     */
    public function getTemplatePathForNamespace($namespace)
    {
        return $this->getDestination() . '/' . $this->namespaceUrlFilters->namespaceUrl($namespace);
    }


    /**
     * @return string
     */
    public function getTemplatePathForClass(ClassReflectionInterface $element)
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForClass($element);
    }


    /**
     * @return string
     */
    public function getTemplatePathForConstant(ConstantReflectionInterface $element)
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForConstant($element);
    }


    /**
     * @return string
     */
    public function getTemplatePathForFunction(FunctionReflectionInterface $element)
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForFunction($element);
    }


    /**
     * @return string
     */
    public function getTemplatePathForSourceElement(ElementReflectionInterface $element)
    {
        return $this->getDestination() . '/' . $this->sourceFilters->sourceUrl($element, false);
    }


    /**
     * @param string $element
     * @return string
     */
    public function getTemplatePathForAnnotationGroup($element)
    {
        return $this->getDestination() . '/' . $this->elementUrlFactory->createForAnnotationGroup($element);
    }


    /**
     * @return string
     */
    private function getDestination()
    {
        return $this->configuration->getOption(CO::DESTINATION);
    }
}
