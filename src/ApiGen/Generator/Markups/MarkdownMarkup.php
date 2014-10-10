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
		$text = $this->block($text);
		return preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $text);
	}


	/**
	 * @param string $text
	 * @return string
	 */
	public function block($text)
	{
		$highlighted = preg_replace_callback('~<(code|pre)>(.+?)</\1>|```php\s(.+?)\n```~s', array($this, 'highlightCb'), $text);
		return $this->markdown->transform($highlighted);
	}


	private function highlightCb(array $match)
	{
		$highlighted = $this->highlighter->highlight(trim(isset($match[3]) ? $match[3] : $match[2]));
		return "<pre>$highlighted</pre>";
	}

}
