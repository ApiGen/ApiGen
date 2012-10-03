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

	/**
	 * Returns root directory.
	 *
	 * @return string
	 */
	public static function getRootDir()
	{
		return realpath(__DIR__ . '/..');
	}

	/**
	 * Initializes ApiGen environment.
	 */
	public static function init()
	{
		// Safe locale and timezone
		setlocale(LC_ALL, 'C');
		if (!ini_get('date.timezone')) {
			date_default_timezone_set('UTC');
		}

		// Check required extensions
		foreach (array('json', 'iconv', 'mbstring', 'tokenizer') as $extension) {
			if (!extension_loaded($extension)) {
				throw new \Exception(sprintf("Required extension missing: %s\n", $extension), 1);
			}
		}

		if (static::isPearPackage()) {
			// PEAR package
			@include 'Nette/loader.php';
			@include 'Texy/texy.php';
		} elseif (static::isComposerPackage()) {
			// Composer package

			$vendorDir = realpath(static::getRootDir() . '/../..');

			@include $vendorDir . '/nette/nette/Nette/loader.php';
			@include $vendorDir . '/dg/texy/texy/texy.php';

			set_include_path(
				$vendorDir . '/kukulich/fshl' . PATH_SEPARATOR .
				$vendorDir . '/andrewsville/php-token-reflection' . PATH_SEPARATOR .
				get_include_path()
			);

		} elseif (static::isStandalonePackage() || static::isGitRepository()) {
			// Standalone package or Git repository

			$rootDir = static::getRootDir();

			@include $rootDir . '/libs/Nette/Nette/loader.php';
			@include $rootDir . '/libs/Texy/texy/texy.php';

			set_include_path(
				$rootDir . '/libs/FSHL' . PATH_SEPARATOR .
				$rootDir . '/libs/TokenReflection' . PATH_SEPARATOR .
				get_include_path()
			);
		} else {
			throw new Exception('Unsupported installation way', 2);
		}

		// Autoload
		spl_autoload_register(function($className) {
			$className = trim($className, '\\');
			$classFileName = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

			if (0 === strpos($className, 'ApiGen\\') && is_file($fileName = Environment::getRootDir() . DIRECTORY_SEPARATOR . $classFileName)) {
				include $fileName;
			} else {
				@include $classFileName;
			}
		});

		// Check required libraries
		if (!class_exists('Nette\\Diagnostics\\Debugger')) {
			throw new \Exception('Could not find Nette framework', 3);
		}
		if (!class_exists('Texy')) {
			throw new \Exception('Could not find Texy! library', 3);
		}
		if (!class_exists('FSHL\\Highlighter')) {
			throw new \Exception('Could not find FSHL library', 3);
		}
		if (!class_exists('TokenReflection\\Broker')) {
			throw new \Exception('Could not find TokenReflection library', 3);
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

	/**
	 * Returns if ApiGen is installed as a Composer package.
	 *
	 * @return boolean
	 */
	public static function isComposerPackage()
	{
		return is_file(__DIR__ . '/../../../autoload.php');
	}

	/**
	 * Returns if ApiGen is installed as a standalone package.
	 *
	 * @return boolean
	 */
	public static function isStandalonePackage()
	{
		return is_dir(__DIR__ . '/../libs') && !static::isGitRepository();
	}

	/**
	 * Returns if ApiGen is installed as Git repository.
	 *
	 * @return boolean
	 */
	public static function isGitRepository()
	{
		return is_file(__DIR__ . '/../.gitmodules');
	}

	/**
	 * Returns if ApiGen is runnning on Windows.
	 *
	 * @return boolean
	 */
	public static function isWindows()
	{
		return 'WIN' === substr(PHP_OS, 0, 3);
	}

	/**
	 * Returns if ApiGen is running in a terminal with colors support.
	 *
	 * @return boolean
	 */
	public static function isTerminalWithColors()
	{
		if (static::isWindows()) {
			return false !== getenv('ANSICON');
		} else {
			return function_exists('posix_isatty') && defined('STDOUT') && @posix_isatty(STDOUT);
		}
	}
}
