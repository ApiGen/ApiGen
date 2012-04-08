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

use Nette\UnexpectedValueException;

/**
 * ApiGen environment helpers.
 */
class Environment
{
	/**
	 * Returns if ApiGen is installed as a PEAR package.
	 *
	 * @return boolean
	 */
	public static function isPearPackage()
	{
		return false === strpos('@php_dir@', '@php_dir');
	}

	/**
	 * Processes and returns command line arguments.
	 *
	 * @param array $argv Command line arguments
	 * @return array
	 * @todo Throw exception if an unexpected argument is found
	 */
	public static function getCliArguments(array $argv)
	{
		$options = array();

		while ($argument = current($argv)) {
			if (preg_match('~^--([a-z][-a-z]*[a-z])(?:=(.+))?$~', $argument, $matches) || preg_match('~^-([a-z])=?(.*)~', $argument, $matches)) {
				$name = $matches[1];

				if (!empty($matches[2])) {
					$value = $matches[2];
				} else {
					$next = next($argv);
					if (false === $next || '-' === $next{0}) {
						prev($argv);
						$value = '';
					} else {
						$value = $next;
					}
				}

				$options[$name][] = $value;
			}

			next($argv);
		}

		$options = array_map(function($value) {
			return 1 === count($value) ? $value[0] : $value;
		}, $options);

		return $options;
	}

	/**
	 * Returns the application name.
	 *
	 * @return string
	 */
	public static function getApplicationName()
	{
		return 'ApiGen ' . VERSION;
	}
}
