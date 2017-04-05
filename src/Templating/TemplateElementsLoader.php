<?php declare(strict_types=1);

namespace ApiGen\Templating;

use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Parser\Elements\AutocompleteElements;

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
     * @var mixed[]
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

    public function addElementsToTemplate(Template $template): void
    {
        $template->setParameters($this->getParameters());
    }

    /**
     * @return mixed[]
     */
    private function getParameters(): array
    {
        if ($this->parameters === null) {
            $parameters = [
                'annotationGroups' => $this->configuration->getOption(ConfigurationOptions::ANNOTATION_GROUPS),
                'namespace' => null,
                'class' => null,
                'constant' => null,
                'function' => null,
                'namespaces' => array_keys($this->elementStorage->getNamespaces()),
                'classes' => array_filter($this->elementStorage->getClasses()),
                'interfaces' => array_filter($this->elementStorage->getInterfaces()),
                'traits' => array_filter($this->elementStorage->getTraits()),
                'exceptions' => array_filter($this->elementStorage->getExceptions()),
                'functions' => array_filter($this->elementStorage->getFunctions()),
                'elements' => $this->autocompleteElements->getElements()
            ];

            $this->parameters = $parameters;
        }

        return $this->parameters;
    }
}
