<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\ClassLikeInfo;
use Nette\Utils\Strings;


final class UrlGenerator
{
	private string $baseDir = '';


	public function getBaseDir(): string
	{
		return $this->baseDir;
	}


	public function setBaseDir(string $baseDir): void
	{
		$this->baseDir = $baseDir;
	}


	public function relative(string $path): string
	{
		return Strings::after(realpath($path), realpath($this->baseDir) . DIRECTORY_SEPARATOR);
	}


	public function classLike(ClassLikeInfo $classLike): string
	{
		return strtr($classLike->name, '\\', '.') . '.html';
	}


	public function namespace(NamespaceInfo $namespace): string
	{
		return 'namespace-' . strtr($namespace->name, '\\', '.') . '.html';
	}


	public function source(string $path): string
	{
		return 'source-' . substr(strtr($this->relative($path), '\\/', '..'), 0, -4) . '.html';
	}


	public function tree(): string
	{
		return 'tree.html';
	}
}
