<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\Markups\Markup;
use ApiGen\Generator\SourceCodeHighlighter;
use Nette;


class TemplateFactory extends Nette\Object
{

	/**
	 * @var Markup
	 */
	private $markup;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Markup $markup, SourceCodeHighlighter $highlighter, Configuration $configuration)
	{
		$this->markup = $markup;
		$this->highlighter = $highlighter;
		$this->configuration = $configuration;
	}


	/**
	 * @return Template
	 */
	public function create()
	{
		$template = new Template($this->markup, $this->highlighter, $this->configuration);
		return $template;
	}

}
