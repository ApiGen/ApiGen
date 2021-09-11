<?php declare(strict_types = 1);

namespace ApiGenX;

use ReflectionClass;


final class Helpers
{
	public static function realPath(string $path): string
	{
		$realPath = realpath($path);

		if ($realPath === false) {
			throw new \RuntimeException("File $path does not exist.");
		}

		return $realPath;
	}


	/**
	 * @param class-string $name
	 */
	public static function classLikePath(string $name): string
	{
		$reflection = new ReflectionClass($name);
		$path = $reflection->getFileName();

		if ($path === false) {
			throw new \RuntimeException("Class-like $name has no path.");
		}

		return $path;
	}
}
