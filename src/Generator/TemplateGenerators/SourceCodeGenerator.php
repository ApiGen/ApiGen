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
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Generator\ConditionalTemplateGenerator;
use ApiGen\Generator\Event\GenerateProgressEvent;
use ApiGen\Generator\Event\GeneratorEvents;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Generator\SourceCodeHighlighter\SourceCodeHighlighter;
use ApiGen\Generator\StepCounter;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\TemplateFactory;


class SourceCodeGenerator implements ConditionalTemplateGenerator, StepCounter
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var ElementStorage
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


	public function __construct(
		Configuration $configuration,
		ElementStorage $elementStorage,
		TemplateFactory $templateFactory,
		RelativePathResolver $relativePathResolver,
		SourceCodeHighlighter $sourceCodeHighlighter,
		EventDispatcherInterface $eventDispatcher
	) {
		$this->configuration = $configuration;
		$this->elementStorage = $elementStorage;
		$this->templateFactory = $templateFactory;
		$this->relativePathResolver = $relativePathResolver;
		$this->sourceCodeHighlighter = $sourceCodeHighlighter;
		$this->eventDispatcher = $eventDispatcher;
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
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption(CO::SOURCE_CODE);
	}


	/**
	 * @return int
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


	private function generateForElement(ElementReflectionInterface $element)
	{
		$template = $this->templateFactory->createNamedForElement(TCO::SOURCE, $element);
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
