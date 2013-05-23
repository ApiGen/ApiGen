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

namespace ApiGen\Config;

use ApiGen;
use ApiGen\Environment;
use Nette\Config\Helpers;

/**
 * Configuration helper.
 */
class Helper extends Helpers
{
	/**
	 * The default configuration file name.
	 *
	 * @var string
	 */
	const DEFAULT_CONFIG_FILENAME = 'apigen.neon';

	/**
	 * The default template configuration file name.
	 *
	 * @var string
	 */
	const DEFAULT_TEMPLATE_CONFIG_FILENAME = 'default/config.neon';

	/**
	 * Returns default configuration file path.
	 *
	 * @return string
	 */
	public static function getDefaultConfigPath()
	{
		return getcwd() . DIRECTORY_SEPARATOR . static::DEFAULT_CONFIG_FILENAME;
	}

	/**
	 * Returns templates directory path.
	 *
	 * @return string
	 */
	public static function getTemplatesDir()
	{
		return Environment::getRootDir() . '/templates';
	}

	/**
	 * Returns default template configuration file path.
	 *
	 * @return string
	 */
	public static function getDefaultTemplateConfigPath()
	{
		return static::getTemplatesDir() . DIRECTORY_SEPARATOR . static::DEFAULT_TEMPLATE_CONFIG_FILENAME;
	}

	/**
	 * Checks if default configuration file exists.
	 *
	 * @return boolean
	 */
	public static function defaultConfigExists()
	{
		return is_file(static::getDefaultConfigPath());
	}

	/**
	 * Returns a file absolute path.
	 *
	 * @param string $relativePath File relative path
	 * @param array $baseDirectories List of base directories
	 * @return string|null
	 */
	public static function getAbsoluteFilePath($relativePath, array $baseDirectories)
	{
		if (preg_match('~/|[a-z]:~Ai', $relativePath) || self::isPathInPhar($relativePath)) {
			// Absolute path already
			return is_file($relativePath) ? $relativePath : null;
		}

		foreach ($baseDirectories as $directory) {
			$fileName = $directory . DIRECTORY_SEPARATOR . $relativePath;
			if (is_file($fileName)) {
				return realpath($fileName);
			}
		}

		return null;
	}

	/**
	 * Returns a directory absolute path.
	 *
	 * @param string $relativePath Directory relative path
	 * @param array $baseDirectories List of base directories
	 * @return string|null
	 */
	public static function getAbsoluteDirectoryPath($relativePath, array $baseDirectories)
	{
	    return self::getAbsoluteFilePath($relativePath, $baseDirectories);
	}

	/**
	 * get realpath (resolve relative paths) for given directory
	 *
	 * @param string $path Relative path to resolve
	 * @return string|boolean Resolved path or false if path does not exist
	 */
	public static function realpath($path)
	{
	    if (file_exists($path)) {
	        if (!self::isPathInPhar($path)) {
	            $path = realpath($path);
	        }
	        return $path;
	    }

	    return false;
	}

	/**
	 * Returns whether the given path is within a phar archive
	 *
	 * @param string $path
	 * @return boolean
	 */
	public static function isPathInPhar($path)
	{
        return (substr($path, 0, 7) === 'phar://');
	}
}
