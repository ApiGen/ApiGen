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

use Texy;
use TexyHtml;

class TexyMarkup implements IMarkup
{
	private $texy;

	public function __construct(Config\Configuration $allowedHtml, ISourceCodeHighlighter $highlighter)
	{
		// @todo DI
		$this->texy = new Texy();
		$this->texy->allowedTags = array_flip($allowedHtml->toArray());
		$this->texy->allowed['list/definition'] = false;
		$this->texy->allowed['phrase/em-alt'] = false;
		$this->texy->allowed['longwords'] = false;
		$this->texy->allowed['typography'] = false;
		$this->texy->linkModule->shorten = false;

		$this->texy->addHandler('beforeParse', function($texy, &$text, $singleLine) {
			$text = preg_replace('~<code>(.+?)</code>~', '#code#\\1#/code#', $text);
		});
		$this->texy->registerLinePattern(
			function($parser, $matches, $name) use ($highlighter) {
				return TexyHtml::el('code', $highlighter->highlight($matches[1]));
			},
			'~#code#(.+?)#/code#~',
			'codeInlineSyntax'
		);
		$this->texy->registerBlockPattern(
			function($parser, $matches, $name) use ($highlighter) {
				if ('code' === $matches[1]) {
					$lines = array_filter(explode("\n", $matches[2]));
					if (!empty($lines)) {
						$firstLine = array_shift($lines);

						$indent = '';
						$li = 0;

						while (isset($firstLine[$li]) && preg_match('~\\s~', $firstLine[$li])) {
							foreach ($lines as $line) {
								if (!isset($line[$li]) || $firstLine[$li] !== $line[$li]) {
									break 2;
								}
							}

							$indent .= $firstLine[$li++];
						}

						if (!empty($indent)) {
							$matches[2] = str_replace(
								"\n" . $indent,
								"\n",
								0 === strpos($matches[2], $indent) ? substr($matches[2], $li) : $matches[2]
							);
						}
					}

					$content = $highlighter->highlight($matches[2]);
				} else {
					$content = htmlspecialchars($matches[2]);
				}

				$content = $parser->getTexy()->protect($content, Texy::CONTENT_BLOCK);
				return TexyHtml::el('pre', $content);
			},
			'~<(code|pre)>(.+?)</\\1>~s',
			'codeBlockSyntax'
		);
	}

	public function line($text)
	{
		return $this->texy->processLine($text);
	}

	public function block($text)
	{
		return $this->texy->process($text);
	}
}
