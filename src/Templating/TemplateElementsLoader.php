<?php

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\AutocompleteElements;
use Closure;

class TemplateElementsLoader
{

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var Configuration
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
        Configuration $configuration,
        AutocompleteElements $autocompleteElements
    ) {
        $this->elementStorage = $elementStorage;
        $this->configuration = $configuration;
        $this->autocompleteElements = $autocompleteElements;
    }


    /**
     * @return Template
     */
    public function addElementsToTemplate(Template $template)
    {
        return $template->setParameters($this->getParameters());
    }


    /**
     * @return Closure
     */
    private function getMainFilter()
    {
        return function (ElementReflectionInterface $element) {
            return $element->isMain();
        };
    }


    /**
     * @return array
     */
    private function getParameters()
    {
        if ($this->parameters === null) {
            $parameters = [
                'annotationGroups' => $this->configuration->getOption(CO::ANNOTATION_GROUPS),
                'namespace' => null,
                'package' => null,
                'class' => null,
                'constant' => null,
                'function' => null,
                'namespaces' => array_keys($this->elementStorage->getNamespaces()),
                'packages' => array_keys($this->elementStorage->getPackages()),
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
