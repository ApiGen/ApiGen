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
use Nette;


abstract class Filters extends Nette\Object
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
			return call_user_func_array(array($this, $name), $args);
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
		$names = array(
			'int' => 'integer',
			'bool' => 'boolean',
			'double' => 'float',
			'void' => '',
			'FALSE' => 'false',
			'TRUE' => 'true',
			'NULL' => 'null',
			'callback' => 'callable'
		);

		// Simple type
		if (isset($names[$name])) {
			return $names[$name];
		}

		// Class, constant or function
		return $trimNamespaceSeparator ? ltrim($name, '\\') : $name;
	}


	/**
	 * Builds a link.
	 *
	 * @param string $url
	 * @param string $text
	 * @param bool $escape If the text should be escaped
	 * @param array $classes List of classes
	 * @return string
	 */
	protected function link($url, $text, $escape = TRUE, array $classes = array())
	{
		return Strings::link($url, $text, $escape, $classes);
	}


	/**
	 * @param string $s
	 * @return string
	 */
	protected function escapeHtml($s)
	{
		return Latte\Runtime\Filters::escapeHtml($s);
	}


	/**
	 * Converts string to url safe characters.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function urlize($string)
	{
		return preg_replace('~[^\w]~', '.', $string);
	}

}
