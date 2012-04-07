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
	 * @throws \Nette\UnexpectedValueException If an unexpected option is found
	 */
	public static function getCliArguments(array $argv)
	{
		$params = array();

		$current = null;
		foreach ($argv as $argument) {
			if (preg_match('~^--([a-z][-a-z]*[a-z])(?:=(.+))?$~', $argument, $matches) || preg_match('~^-([a-z])=?(.*)~', $argument, $matches)) {
				if (isset($matches[2])) {
					$current = null;
					$params[$matches[1]][] = $matches[2];
				} else {
					$current = $matches[1];
					$params[$current][] = true;
				}
			} elseif (null !== $current) {
				array_pop($params[$current]);
				$params[$current][] = $argument;
				$current = null;
			} else {
				throw new UnexpectedValueException(sprintf('Invalid option "%s" found.', $argument));
			}
		}

		return $params;
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
