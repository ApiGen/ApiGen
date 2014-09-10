<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Markups;

use ApiGen\Generator\SourceCodeHighlighter;
use Michelf\MarkdownExtra;


class MarkdownMarkup implements Markup
{
	/**
	 * @var MarkdownExtra
	 */
	private $markdown;

	/**
	 * @var SourceCodeHighlighter
	 */
	private $highlighter;


	public function __construct(MarkdownExtra $markdown, SourceCodeHighlighter $highlighter)
	{
		$this->markdown = $markdown;
		$this->highlighter = $highlighter;
	}


	/**
	 * @param string $text
	 * @return string
	 */
	public function line($text)
	{
		return $this->block($text);
	}


	/**
	 * @param $text
	 * @return string
	 */
	public function block($text)
	{
		// Match all <code> or <pre> blocks
		preg_match_all(
			'~<(code|pre)>(.+?)</\\1>~sm',
			$text,
			$matches,
			PREG_OFFSET_CAPTURE | PREG_SET_ORDER
		);

		$offset = 0;
		foreach ($matches as $match) {
			$tagName = isset($match[1][0]) ? $match[1][0] : NULL;
			if ($tagName === 'code' || $tagName === 'pre') {
				$position  = $match[2][1];
				$preCode = $match[2][0];
				$preLen = strlen($preCode);

				// Wraps with <pre> the formatted code
				$postCode = $this->highlighter->highlight(trim($preCode));
				if ($tagName !== 'pre') {
					$postCode = '<pre>' . $postCode . '</pre>';
				}

				// Replace the new formatted code instead of the old
				$text = substr($text, 0, $offset + $position)
					. $postCode
					. substr($text, $offset + $position + $preLen);

				// The new piece of code we injected might be of a different
				// length, so all our positions need to be shifted by that difference
				$offset += strlen($postCode) - strlen($preCode);
			}
		}

		return $this->markdown->transform($text);
	}

}
