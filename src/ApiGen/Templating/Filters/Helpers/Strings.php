<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters\Helpers;

use Latte;
use Latte\Runtime\Filters;
use Nette;


class Strings extends Nette\Object
{

	/**
	 * Parses annotation value.
	 *
	 * @param string $value
	 * @return array
	 */
	public static function split($value)
	{
		return preg_split('~\s+|$~', $value, 2);
	}


	/**
	 * Builds a link.
	 *
	 * @param string $url
	 * @param string $text
	 * @param boolean $escape If the text should be escaped
	 * @param array $classes List of classes
	 * @return string
	 */
	public static function link($url, $text, $escape = TRUE, array $classes = array())
	{
		$class = ! empty($classes) ? sprintf(' class="%s"', implode(' ', $classes)) : '';
		return sprintf('<a href="%s"%s>%s</a>', $url, $class, $escape ? Filters::escapeHtml($text) : $text);
		// @todo, use Html class
	}

}
