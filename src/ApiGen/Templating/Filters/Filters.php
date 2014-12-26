<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters;

use ApiGen\Templating\Filters\Helpers\Strings;
use Latte;
use Nette\Utils\Html;


abstract class Filters
{

	/**
	 * Calls public method with args if exists and passes args.
	 *
	 * @param string $name
	 * @throws \Exception
	 * @return mixed
	 */
	public function loader($name)
	{
		if (method_exists($this, $name)) {
			$args = array_slice(func_get_args(), 1);
			return call_user_func_array([$this, $name], $args);
		}
		return NULL;
	}


	/**
	 * Returns unified type value definition (class name or member data type).
	 *
	 * @param string $name
	 * @param bool $trimNamespaceSeparator
	 * @return string
	 */
	protected function getTypeName($name, $trimNamespaceSeparator = TRUE)
	{
		$names = [
			'int' => 'integer',
			'bool' => 'boolean',
			'double' => 'float',
			'void' => '',
			'FALSE' => 'false',
			'TRUE' => 'true',
			'NULL' => 'null',
			'callback' => 'callable'
		];

		// Simple type
		if (isset($names[$name])) {
			return $names[$name];
		}

		// Class, constant or function
		return $trimNamespaceSeparator ? ltrim($name, '\\') : $name;
	}


	/**
	 * @param string $url
	 * @param string $text
	 * @param bool $escape
	 * @param array $classes
	 * @return string
	 */
	protected function link($url, $text, $escape = TRUE, array $classes = [])
	{
		$link = Html::el('a')->href($url)
			->setText($escape ? self::escapeHtml($text) : $text)
			->addAttributes(['class' => $classes]);
		return (string) $link;
	}


	/**
	 * @param string $html
	 * @return string
	 */
	protected function escapeHtml($html)
	{
		return Latte\Runtime\Filters::escapeHtml($html);
	}


	/**
	 * @param string $string
	 * @return string
	 */
	protected function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}


	/**
	 * @param string $url
	 * @return string
	 */
	private function url($url)
	{
		return rawurlencode($url);
	}

}
