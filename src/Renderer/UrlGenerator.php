<?php declare(strict_types = 1);

namespace ApiGenX\Renderer;

use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\ConstantInfo;
use ApiGenX\Info\EnumCaseInfo;
use ApiGenX\Info\MemberInfo;
use ApiGenX\Info\MethodInfo;
use ApiGenX\Info\PropertyInfo;


final class UrlGenerator
{
	public function __construct(
		private string $baseDir,
		private string $baseUrl,
	) {
	}


	public function getRelativePath(string $path): string
	{
		if (str_starts_with($path, $this->baseDir)) {
			return substr($path, strlen($this->baseDir));

		} else {
			throw new \LogicException("{$path} does not start with {$this->baseDir}");
		}
	}


	public function getAssetUrl(string $name): string
	{
		return $this->baseUrl . $this->getAssetPath($name);
	}


	public function getAssetPath(string $name): string
	{
		return "assets/$name";
	}


	public function getIndexUrl(): string
	{
		return $this->baseUrl . $this->getIndexPath();
	}


	public function getIndexPath(): string
	{
		return 'index.html';
	}


	public function getTreeUrl(): string
	{
		return $this->baseUrl . $this->getTreePath();
	}


	public function getTreePath(): string
	{
		return 'tree.html';
	}


	public function getNamespaceUrl(NamespaceIndex $namespace): string
	{
		return $this->baseUrl . $this->getNamespacePath($namespace);
	}


	public function getNamespacePath(NamespaceIndex $namespace): string
	{
		return 'namespace-' . strtr($namespace->name->full, '\\', '.') . '.html';
	}


	public function getClassLikeUrl(ClassLikeInfo $classLike): string
	{
		return $this->baseUrl . $this->getClassLikePath($classLike);
	}


	public function getClassLikePath(ClassLikeInfo $classLike): string
	{
		return strtr($classLike->name->full, '\\', '.') . '.html';
	}


	public function getClassLikeSourceUrl(ClassLikeInfo $classLike): string
	{
		assert($classLike->file !== null);
		return $this->getSourceUrl($classLike->file, $classLike->startLine, null); // intentionally not passing endLine
	}


	public function getMemberUrl(ClassLikeInfo $classLike, MemberInfo $member): string
	{
		return $this->getClassLikeUrl($classLike) . '#' . $this->getMemberAnchor($member);
	}


	public function getMemberAnchor(MemberInfo $member): string
	{
		if ($member instanceof ConstantInfo || $member instanceof EnumCaseInfo) {
			return $member->name;

		} elseif ($member instanceof PropertyInfo) {
			return '$' . $member->name;

		} elseif ($member instanceof MethodInfo) {
			return '_' . $member->name;

		} else {
			throw new \LogicException(sprintf('Unexpected member type %s', get_debug_type($member)));
		}
	}


	public function getMemberSourceUrl(ClassLikeInfo $classLike, MemberInfo $member): string
	{
		assert($classLike->file !== null);
		return $this->getSourceUrl($classLike->file, $member->startLine, $member->endLine);
	}


	public function getSourceUrl(string $path, ?int $startLine, ?int $endLine): string
	{
		if ($startLine === null) {
			$fragment = '';

		} elseif ($endLine === null || $endLine === $startLine) {
			$fragment = "#$startLine";

		} else {
			$fragment = "#$startLine-$endLine";
		}

		return $this->baseUrl . $this->getSourcePath($path) . $fragment;
	}


	public function getSourcePath(string $path): string
	{
		return 'source-' . substr(strtr($this->getRelativePath($path), '\\/', '..'), 0, -4) . '.html';
	}
}
