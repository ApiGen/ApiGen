<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Markups;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\SourceCodeHighlighter;
use Texy;
use TexyHtml;


class TexyMarkup implements Markup
{
	/**
	 * @var Texy
	 */
	private $texy;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;


	public function __construct(Texy $texy, SourceCodeHighlighter $highlighter)
	{
		$this->texy = $texy;
		$this->highlighter = $highlighter;
	}


	public function setup()
	{
		$this->texy->allowedTags = Texy::ALL;
		$this->texy->allowed['list/definition'] = FALSE;
		$this->texy->allowed['phrase/em-alt'] = FALSE;
		$this->texy->allowed['longwords'] = FALSE;
		$this->texy->allowed['typography'] = FALSE;
		$this->texy->linkModule->shorten = FALSE;
		// Highlighting <code>, <pre>
		$this->texy->addHandler('beforeParse', function ($texy, &$text, $singleLine) {
			$text = preg_replace('~<code>(.+?)</code>~', '#code#\\1#/code#', $text);
		});

		$highlighter = $this->highlighter;
		$this->texy->registerLinePattern(
			function ($parser, $matches, $name) use ($highlighter) {
				$content = $parser->getTexy()->protect($highlighter->highlight($matches[1]), \Texy::CONTENT_MARKUP);
				return \TexyHtml::el('code', $content);
			},
			'~#code#(.+?)#/code#~',
			'codeInlineSyntax'
		);

		$this->texy->registerBlockPattern(
			function ($parser, $matches, $name) use ($highlighter) {
				if ('code' === $matches[1]) {
					$lines = array_filter(explode("\n", $matches[2]));
					if ( ! empty($lines)) {
						$firstLine = array_shift($lines);

						$indent = '';
						$li = 0;

						while (isset($firstLine[$li]) && preg_match('~\s~', $firstLine[$li])) {
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

				$content = $parser->getTexy()->protect($content, \Texy::CONTENT_BLOCK);
				return \TexyHtml::el('pre', $content);
			},
			'~<(code|pre)>(.+?)</\1>~s',
			'codeBlockSyntax'
		);
	}


	/**
	 * @param string $text
	 * @return string
	 */
	public function line($text)
	{
		return $this->texy->processLine($text);
	}


	/**
	 * @param string $text
	 * @return string
	 */
	public function block($text)
	{
		return $this->texy->process($text);
	}

}
