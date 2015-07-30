<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceAndPackageLoader;
use ApiGen\Templating\TemplateFactory;

class NamespaceGenerator implements ConditionalTemplateGeneratorInterface, StepCounterInterface
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
     * @var NamespaceAndPackageLoader
     */
    private $namespaceAndPackageLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(
        TemplateFactory $templateFactory,
        ElementStorageInterface $elementStorage,
        NamespaceAndPackageLoader $namespaceAndPackageLoader,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templateFactory = $templateFactory;
        $this->elementStorage = $elementStorage;
        $this->namespaceAndPackageLoader = $namespaceAndPackageLoader;
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        foreach ($this->elementStorage->getNamespaces() as $name => $namespace) {
            $template = $this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $name);
            $template = $this->namespaceAndPackageLoader->loadTemplateWithNamespace($template, $name, $namespace);
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


    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return (bool) $this->elementStorage->getNamespaces();
    }
}
