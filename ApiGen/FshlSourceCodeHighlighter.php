<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use FSHL;

class FshlSourceCodeHighlighter implements ISourceCodeHighlighter
{
	private $highlighter;

	public function __construct()
	{
		// @todo DI
		$this->highlighter = new FSHL\Highlighter(new FSHL\Output\Html());
		$this->highlighter->setLexer(new FSHL\Lexer\Php());
	}

	public function highlight($sourceCode, $lines = false)
	{
		$options = FSHL\Highlighter::OPTION_TAB_INDENT;

		if ($lines) {
			$options |= FSHL\Highlighter::OPTION_LINE_COUNTER;
		}

		$this->highlighter->setOptions($options);

		return $this->highlighter->highlight($sourceCode);
	}
}
