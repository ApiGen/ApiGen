<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Generator\Highlighter\SourceCodeHighlighter;
use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Reflection\ReflectionElement;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateNavigator;
use Nette;
use stdClass;


class SourceCodeGenerator extends Nette\Object
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


	public function __construct(
		CharsetConvertor $charsetConvertor,
		RelativePathResolver $relativePathResolver,
		SourceCodeHighlighter $sourceCodeHighlighter,
		TemplateNavigator $templateNavigator
	) {
		$this->charsetConvertor = $charsetConvertor;
		$this->relativePathResolver = $relativePathResolver;
		$this->sourceCodeHighlighter = $sourceCodeHighlighter;
		$this->templateNavigator = $templateNavigator;
	}


	/**
	 * @param Template|stdClass $template
	 * @param ReflectionElement $element
	 */
	public function generateForElement(Template $template, $element)
	{
		$template->fileName = $this->relativePathResolver->getRelativePath($element->getFileName());
		$content = $this->charsetConvertor->convertFile($element->getFileName());
		$template->source = $this->sourceCodeHighlighter->highlightAndAddLineNumbers($content);

		$template->setFile($this->templateNavigator->getTemplatePath('source'))
			->setSavePath($this->templateNavigator->getTemplatePathForSourceElement($element))
			->save();
	}

}
