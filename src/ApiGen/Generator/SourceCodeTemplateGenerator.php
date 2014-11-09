<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Configuration\Configuration;
use ApiGen\Elements\ElementStorage;
use ApiGen\Generator\Highlighter\SourceCodeHighlighter;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use Nette;


class SourceCodeTemplateGenerator extends Nette\Object implements TemplateGenerator
{

	/**
	 * @var CharsetConvertor
	 */
	private $charsetConvertor;

	/**
	 * @var RelativePathResolver
	 */
	private $relativePathResolver;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $sourceCodeHighlighter;

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;

	/**
	 * @var ElementStorage
	 */
	private $elementStorage;


	public function __construct(
		Configuration $configuration,
		CharsetConvertor $charsetConvertor,
		ElementStorage $elementStorage,
		RelativePathResolver $relativePathResolver,
		SourceCodeHighlighter $sourceCodeHighlighter,
		TemplateNavigator $templateNavigator,
		TemplateFactory $templateFactory
	) {
		$this->configuration = $configuration;
		$this->charsetConvertor = $charsetConvertor;
		$this->elementStorage = $elementStorage;
		$this->relativePathResolver = $relativePathResolver;
		$this->sourceCodeHighlighter = $sourceCodeHighlighter;
		$this->templateNavigator = $templateNavigator;
		$this->templateFactory = $templateFactory;
	}


	public function generate()
	{
		foreach ($this->elementStorage->getElements() as $type => $elementList) {
			foreach ($elementList as $element) {
				/** @var ReflectionElement $element */
				if ($element->isTokenized()) {
					$this->generateForElement($element);
				}
			}
		}
	}


	private function generateForElement(ReflectionElement $element)
	{
		$template = $this->templateFactory->createNamedForElement('source', $element);
		$template->fileName = $this->relativePathResolver->getRelativePath($element->getFileName());
		$content = $this->charsetConvertor->convertFileToUtf($element->getFileName());
		$template->source = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);
		$template->save();
	}


	/**
	 * @return bool
	 */
	public function isAllowed()
	{
		return $this->configuration->getOption('sourceCode');
	}

}
