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

use Nette\Utils\LimitedScope;

/**
 * ApiGen environment helpers.
 */
class Environment
{
	/**
	 * Returns the application name.
	 *
	 * @return string
	 */
	public static function getApplicationName()
	{
		return 'ApiGen';
	}

	/**
	 * Returns the application version.
	 *
	 * @return string
	 */
	public static function getApplicationVersion()
	{
		return '3.0dev';
	}

	public static function getRootDir()
	{
		return realpath(__DIR__ . '/..');
	}

	public static function init()
	{
		// Safe locale and timezone
		setlocale(LC_ALL, 'C');
		if (!ini_get('date.timezone')) {
			date_default_timezone_set('UTC');
		}

		$rootDir = static::getRootDir();

		spl_autoload_register(function($className) use ($rootDir) {
			if ('ApiGen\\' === substr($className, 0, 7) && is_file($fileName = $rootDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className . '.php'))) {
				LimitedScope::load($fileName);
			} else {
				@LimitedScope::load(str_replace('\\', DIRECTORY_SEPARATOR, $className . '.php'));
			}
		});

		foreach (array('json', 'iconv', 'mbstring', 'tokenizer') as $extension) {
			if (!extension_loaded($extension)) {
				throw new \Exception(sprintf("Required extension missing: %s\n", $extension), 1);
			}
		}

		if (Environment::isPearPackage()) {
			// PEAR package
			@include 'Nette/loader.php';
			@include 'Texy/texy.php';
		} else {
			// Standalone package
			@include $rootDir . '/libs/Nette/Nette/loader.php';
			@include $rootDir . '/libs/Texy/texy/texy.php';

			set_include_path(
				$rootDir . '/libs/FSHL' . PATH_SEPARATOR .
				$rootDir . '/libs/TokenReflection' . PATH_SEPARATOR .
				get_include_path()
			);
		}

		if (!class_exists('Nette\\Diagnostics\\Debugger', FALSE)) {
			throw new \Exception('Could not find Nette framework', 2);
		}
		if (!class_exists('Texy')) {
			throw new \Exception('Could not find Texy! library', 2);
		}
		if (!class_exists('FSHL\\Highlighter')) {
			throw new \Exception('Could not find FSHL library', 2);
		}
		if (!class_exists('TokenReflection\\Broker')) {
			throw new \Exception('Could not find TokenReflection library', 2);
		}
	}

	/**
	 * Returns if ApiGen is installed as a PEAR package.
	 *
	 * @return boolean
	 */
	public static function isPearPackage()
	{
		return false === strpos('@php_dir@', '@php_dir');
	}
}
