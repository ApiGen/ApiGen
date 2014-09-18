<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use FSHL;
use Nette;


class FshlSourceCodeHighlighter extends Nette\Object implements SourceCodeHighlighter
{

	/**
	 * @var FSHL\Highlighter
	 */
	private $highlighter;


	public function __construct(FSHL\Highlighter $highlighter)
	{
		$this->highlighter = $highlighter;
	}


	/**
	 * @param string $sourceCode
	 * @param bool $lines
	 * @return string
	 */
	public function highlight($sourceCode, $lines = TRUE)
	{
		$options = FSHL\Highlighter::OPTION_TAB_INDENT;

		if ($lines) {
			$options |= FSHL\Highlighter::OPTION_LINE_COUNTER;
		}

		$this->highlighter->setOptions($options);

		return $this->highlighter->highlight($sourceCode);
	}

}
