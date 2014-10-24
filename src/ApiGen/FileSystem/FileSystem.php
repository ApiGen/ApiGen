<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;


class FileSystem
{

	/**
	 * @param string $file
	 * @return bool
	 */
	public static function isPhar($file)
	{
		return (bool) preg_match('~\\.phar(?:\\.zip|\\.tar|(?:(?:\\.tar)?(?:\\.gz|\\.bz2))|$)~i', $file);
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
	 * @param string $path Path
	 * @return string
	 */
	public static function pharPath($path)
	{
		return 'phar://' . $path;
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function forceDir($path)
	{
		@mkdir(dirname($path), 0755, TRUE);
		return $path;
	}


	/**
	 * @param string $path
	 * @return bool
	 */
	public static function deleteDir($path)
	{
		if ( ! is_dir($path)) {
			return TRUE;
		}

		foreach (Nette\Utils\Finder::find('*')->from($path)->childFirst() as $item) {
			/** @var \SplFileInfo $item */
			if ($item->isDir()) {
				if ( ! @rmdir($item)) {
					return FALSE;
				}

			} elseif ($item->isFile()) {
				if ( ! @unlink($item)) {
					return FALSE;
				}
			}
		}

		if ( ! @rmdir($path)) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * @param string $relativePath
	 * @param array $baseDirectories List of base directories
	 * @return string|NULL
	 */
	public static function getAbsolutePath($relativePath, array $baseDirectories)
	{
		if (preg_match('~/|[a-z]:~Ai', $relativePath)) { // absolute path already
			return $relativePath;
		}

		foreach ($baseDirectories as $directory) {
			$fileName = $directory . DS . $relativePath;
			if (is_file($fileName)) {
				return realpath($fileName);
			}
		}

		return NULL;
	}

}
