<?php

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceLoader;
use ApiGen\Templating\TemplateFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractElementGenerator implements TemplateGeneratorInterface, StepCounterInterface
{

    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var ElementStorageInterface
     */
    protected $elementStorage;

    /**
     * @var NamespaceLoader
     */
    protected $namespaceLoader;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;


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
}
