<?php declare(strict_types = 1);

namespace ApiGenX\Renderer\Latte;

use Latte;

use function is_file;


class LatteCascadingLoader extends Latte\Loaders\FileLoader
{
	/**
	 * @param string[] $baseDirs
	 */
	public function __construct(private array $baseDirs)
	{
		parent::__construct();
	}


	public function getContent(string $name): string
	{
		foreach ($this->baseDirs as $baseDir) {
			$path = $baseDir . '/' . $name;

			if (is_file($path)) {
				return parent::getContent($path);
			}
		}

		throw new Latte\RuntimeException("Missing template file '$name'.");
	}


	public function isExpired(string $name, int $time): bool
	{
		foreach ($this->baseDirs as $baseDir) {
			$path = $baseDir . '/' . $name;

			if (is_file($path)) {
				return parent::isExpired($path, $time);
			}
		}

		throw new Latte\RuntimeException("Missing template file '$name'.");
	}


	public function getUniqueId(string $name): string
	{
		foreach ($this->baseDirs as $baseDir) {
			$path = $baseDir . '/' . $name;

			if (is_file($path)) {
				return parent::getUniqueId($path);
			}
		}

		throw new Latte\RuntimeException("Missing template file '$name'.");
	}
}
