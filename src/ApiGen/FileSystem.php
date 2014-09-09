<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;


class FileSystem
{

	/**
	 * @param $file
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
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$path = str_replace('phar:\\\\', 'phar://', $path);
		return $path;
	}

	/**
	 * Removes phar:// from the path.
	 * @param string $path Path
	 * @return string
	 */
	public static function unPharPath($path)
	{
		if (0 === strpos($path, 'phar://')) {
			$path = substr($path, 7);
		}
		return $path;
	}


	/**
	 * Adds phar:// to the path.
	 * @param string $path Path
	 * @return string
	 */
	public static function pharPath($path)
	{
		return 'phar://' . $path;
	}


	/**
	 * Ensures a directory is created.
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
				if (!@rmdir($item)) {
					return FALSE;
				}

			} elseif ($item->isFile()) {
				if (!@unlink($item)) {
					return FALSE;
				}
			}
		}

		if (!@rmdir($path)) {
			return FALSE;
		}

		return TRUE;
	}

}
