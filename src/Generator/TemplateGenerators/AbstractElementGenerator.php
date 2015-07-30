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
use ApiGen\Contracts\Generator\TemplateGenerators\TemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceAndPackageLoader;
use ApiGen\Templating\TemplateFactory;

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
     * @var NamespaceAndPackageLoader
     */
    protected $namespaceAndPackageLoader;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;


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
}
