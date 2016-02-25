<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Generator\StepCounterInterface;
use ApiGen\Contracts\Generator\TemplateGenerators\ConditionalTemplateGeneratorInterface;
use ApiGen\Contracts\Parser\Elements\ElementStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Generator\TemplateGenerators\Loaders\NamespaceAndPackageLoader;
use ApiGen\Templating\TemplateFactory;

class SourceCodeGenerator implements ConditionalTemplateGeneratorInterface, StepCounterInterface
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ElementStorageInterface
     */
    private $elementStorage;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var RelativePathResolver
     */
    private $relativePathResolver;

    /**
     * @var SourceCodeHighlighter
     */
    private $sourceCodeHighlighter;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var NamespaceAndPackageLoader
     */
    protected $namespaceAndPackageLoader;


    public function __construct(
        Configuration $configuration,
        ElementStorageInterface $elementStorage,
        TemplateFactory $templateFactory,
        RelativePathResolver $relativePathResolver,
        SourceCodeHighlighter $sourceCodeHighlighter,
        EventDispatcherInterface $eventDispatcher,
        NamespaceAndPackageLoader $namespaceAndPackageLoader
    ) {
        $this->configuration = $configuration;
        $this->elementStorage = $elementStorage;
        $this->templateFactory = $templateFactory;
        $this->relativePathResolver = $relativePathResolver;
        $this->sourceCodeHighlighter = $sourceCodeHighlighter;
        $this->eventDispatcher = $eventDispatcher;
        $this->namespaceAndPackageLoader = $namespaceAndPackageLoader;
    }


    public function generate()
    {
        foreach ($this->elementStorage->getElements() as $type => $elementList) {
            foreach ($elementList as $element) {
                /** @var ElementReflectionInterface $element */
                if ($element->isTokenized()) {
                    $this->generateForElement($element);

                    $this->eventDispatcher->dispatch(new GenerateProgressEvent(GeneratorEvents::ON_GENERATE_PROGRESS));
                }
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getStepCount()
    {
        $tokenizedFilter = function (ClassReflectionInterface $class) {
            return $class->isTokenized();
        };

        $count = count(array_filter($this->elementStorage->getClasses(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getInterfaces(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getTraits(), $tokenizedFilter))
            + count(array_filter($this->elementStorage->getExceptions(), $tokenizedFilter))
            + count($this->elementStorage->getConstants())
            + count($this->elementStorage->getFunctions());

        return $count;
    }


    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->configuration->getOption(CO::SOURCE_CODE);
    }


    private function generateForElement(ElementReflectionInterface $element)
    {
        $template = $this->templateFactory->createNamedForElement('source', $element);
        $template = $this->namespaceAndPackageLoader->loadTemplateWithElementNamespaceOrPackage($template, $element);
        $template->setParameters([
            'fileName' => $this->relativePathResolver->getRelativePath($element->getFileName()),
            'source' => $this->getHighlightedCodeFromElement($element)
        ]);
        $template->save();
    }


    /**
     * @return string
     */
    private function getHighlightedCodeFromElement(ElementReflectionInterface $element)
    {
        $content = file_get_contents($element->getFileName());
        return $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
    }
}
