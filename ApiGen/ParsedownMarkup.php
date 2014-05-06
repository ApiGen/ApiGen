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

use Parsedown;

class ParsedownMarkup implements IMarkup
{
	private $pd;
	private $highlight;

	public function __construct(Config\Configuration $allowedHtml, ISourceCodeHighlighter $highlighter)
	{
		$this->pd = new Parsedown();

		$this->highlight = function($matches) use ($highlighter) {
			return '<code>' .
			       htmlspecialchars_decode($highlighter->highlight($matches[1]), ENT_NOQUOTES) .
			       '</code>';
		};
	}

	public function line($line)
	{
		return $this->pd->line($line);
	}

	public function block($text)
	{
		$content = $this->pd->text($text);
		$content = preg_replace_callback('~<code>(.+?)</code>~s', $this->highlight, $content);
		return $content;
	}
}
