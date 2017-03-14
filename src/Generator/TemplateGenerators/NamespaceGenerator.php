<?php

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\TemplateFactory;

class NamespaceGenerator implements TemplateGeneratorInterface, StepCounterInterface
{

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var NamespaceLoader
     */
    private $namespaceLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(
        TemplateFactory $templateFactory,
        ElementStorageInterface $elementStorage,
        NamespaceLoader $namespaceLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->namespaceLoader = $namespaceLoader;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
            $template = $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $name);
            $template = $this->namespaceLoader->loadTemplateWithNamespace($template, $name, $namespace);
            $template->save();

            $this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getStepCount()
    {
        return count($this->elementStorage->getNamespaces());
    }
}
