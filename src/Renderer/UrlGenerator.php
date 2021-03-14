<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ElementInfo;


final class UrlGenerator
{
	public function __construct(private string $baseDir)
	{
	}


	public function relative(string $path): string
	{
		if (str_starts_with($path, $this->baseDir)) {
			return substr($path, strlen($this->baseDir));

		} else {
			throw new \LogicException("{$path} does not start with {$this->baseDir}");
		}
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


	public function index(): string
	{
		return 'index.html';
	}


	public function tree(): string
	{
		return 'tree.html';
	}
}
