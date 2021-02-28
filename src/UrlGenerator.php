<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ElementInfo;
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
		return Strings::after(realpath($path), realpath($this->baseDir) . DIRECTORY_SEPARATOR) ?? '#'; // TODO: normalize else where
	}


	public function element(ElementInfo $info): string
	{
		if ($info instanceof ClassLikeInfo) {
			return $this->classLike($info);

		} elseif ($info instanceof NamespaceIndex) {
			return $this->namespace($info);

		} else {
			throw new \LogicException();
		}
	}


	public function classLike(ClassLikeInfo $classLike): string
	{
		return strtr($classLike->name->full, '\\', '.') . '.html';
	}


	public function namespace(NamespaceIndex $namespace): string
	{
		return 'namespace-' . strtr($namespace->name->full, '\\', '.') . '.html';
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
