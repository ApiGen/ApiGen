<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use Closure;

final class TemplateElementsLoader
{

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var AutocompleteElements
     */
    private $autocompleteElements;

    /**
     * @var array
     */
    private $parameters;


    public function __construct(
        ElementStorageInterface $elementStorage,
        ConfigurationInterface $configuration,
        AutocompleteElements $autocompleteElements
    ) {
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->autocompleteElements = $autocompleteElements;
    }


    public function addElementsToTemplate(Template $template): Template
    {
        return $template->setParameters($this->getParameters());
    }


    private function getMainFilter(): Closure
    {
        return function (ElementReflectionInterface $element) {
            return $element->isMain();
        };
    }


    private function getParameters(): array
    {
        if ($this->parameters === null) {
            $parameters = [
                'annotationGroups' => $this->configuration->getOption(CO::ANNOTATION_GROUPS),
                'namespace' => null,
                'package' => null, // removed, but for BC with Themes
                'class' => null,
                'constant' => null,
                'function' => null,
                'namespaces' => array_keys($this->elementStorage->getNamespaces()),
                'packages' => [], // removed, but for BC with Themes
                'classes' => array_filter($this->elementStorage->getClasses(), $this->getMainFilter()),
                'interfaces' => array_filter($this->elementStorage->getInterfaces(), $this->getMainFilter()),
                'traits' => array_filter($this->elementStorage->getTraits(), $this->getMainFilter()),
                'exceptions' => array_filter($this->elementStorage->getExceptions(), $this->getMainFilter()),
                'constants' => array_filter($this->elementStorage->getConstants(), $this->getMainFilter()),
                'functions' => array_filter($this->elementStorage->getFunctions(), $this->getMainFilter()),
                'elements' => $this->autocompleteElements->getElements()
            ];

            $this->parameters = $parameters;
        }
        return $this->parameters;
    }
}
