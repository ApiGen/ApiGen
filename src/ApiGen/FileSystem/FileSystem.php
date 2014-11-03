<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;
use Tester\Helpers;


class FileSystem
{

	/**
	 * @param string $file
	 * @return bool
	 */
	public static function isPhar($file)
	{
		$pharMask = '~\\.phar(?:\\.zip|\\.tar|(?:(?:\\.tar)?(?:\\.gz|\\.bz2))|$)~i';
		return (bool) preg_match($pharMask, $file);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function normalizePath($path)
	{
		$path = str_replace(array('/', '\\'), DS, $path);
		$path = str_replace('phar:\\\\', 'phar://', $path);
		return $path;
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function unPharPath($path)
	{
		if (strpos($path, 'phar://') === 0) {
			$path = substr($path, 7);
		}
		return $path;
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function pharPath($path)
	{
		return 'phar://' . $path;
	}


	/**
	 * @param string $path
	 */
	public static function createDirForPath($path)
	{
		$dir = dirname($path);
		if ( ! is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}
	}


	/**
	 * @param string $dir
	 */
	public static function deleteDir($dir)
	{
		Helpers::purge($dir);
		rmdir($dir);
	}


	/**
	 * @param string $relativePath
	 * @param array $baseDirectories List of base directories
	 * @return string|NULL
	 */
	public static function getAbsolutePath($relativePath, array $baseDirectories = array())
	{
		if (preg_match('~/|[a-z]:~Ai', $relativePath)) { // absolute path already
			return $relativePath;
		}

		if (count($baseDirectories)) {
			foreach ($baseDirectories as $directory) {
				$fileName = $directory . DS . $relativePath;
				if (is_file($fileName)) {
					return realpath($fileName);
				}
			}
		}

		$path = FileSystem::normalizePath($relativePath);
		if ((strpos($path, 'phar://') !== 0) && file_exists($path)) {
			return realpath($path);
		}

		return NULL;
	}

}
