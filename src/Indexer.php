<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\TraitInfo;


final class Indexer
{
	public function indexFile(Index $index, ?string $file, bool $primary): void
	{
		if ($file === null) {
			return;
		}

		$file = realpath($file);
		$index->files[$file] ??= new FileInfo($file, $primary);
	}


	public function indexNamespace(Index $index, string $namespace, string $namespaceLower): void
	{
		if (isset($index->namespace[$namespaceLower])) {
			return;
		}

		$info = new NamespaceInfo($namespace);

		if ($namespaceLower !== '') {
			$this->indexNamespace($index, $info->namespace, $info->namespaceLower);
		}

		$index->namespace[$namespaceLower] = $info;
		$index->namespace[$info->namespaceLower]->children[$namespaceLower] = $info;
	}


	public function indexClassLike(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->classLike[$info->nameLower])) {
			return; // ignore duplicates
		}

		$index->classLike[$info->nameLower] = $info;

		foreach ($info->constants as $constantName => $_) {
			$index->constants[$constantName][] = $info;
		}

		foreach ($info->properties as $propertyName => $_) {
			$index->properties[$propertyName][] = $info;
		}

		foreach ($info->methods as $methodLowerName => $_) {
			$index->methods[$methodLowerName][] = $info;
		}

		if ($info instanceof ClassInfo) {
			$this->indexClass($info, $index);

		} elseif ($info instanceof InterfaceInfo) {
			$this->indexInterface($info, $index);

		} elseif ($info instanceof TraitInfo) {
			$this->indexTrait($info, $index);

		} else {
			throw new \LogicException();
		}
	}


	public function postProcess(Index $index): void
	{
		// instance of
		foreach ([$index->class, $index->interface] as $infos) {
			foreach ($infos as $info) {
				$this->indexInstanceOf($index, $info);
			}
		}

		// exceptions
		foreach ($index->namespace as $namespaceIndex) {
			foreach ($namespaceIndex->class as $info) {
				if ($info->isThrowable($index)) {
					unset($namespaceIndex->class[$info->nameShortLower]);
					$namespaceIndex->exception[$info->nameShortLower] = $info;
				}
			}
		}

		// method overrides
		foreach ($index->classExtends[''] as $info) {
			$this->indexClassMethodOverrides($index, $info, [], []);
		}

		// sort
		$this->sort($index);
	}


	private function indexClass(ClassInfo $info, Index $index): void
	{
		$index->class[$info->nameLower] = $info;
		$index->namespace[$info->namespaceLower]->class[$info->nameShortLower] = $info;
		$index->classExtends[$info->extends ?? ''][$info->nameLower] = $info;

		foreach ($info->implements as $interfaceName) {
			$index->classImplements[$interfaceName][$info->nameLower] = $info;
		}
	}


	private function indexInterface(InterfaceInfo $info, Index $index): void
	{
		$index->interface[$info->nameLower] = $info;
		$index->namespace[$info->namespaceLower]->interface[$info->nameShortLower] = $info;

		if ($info->extends) {
			foreach ($info->extends as $interfaceName) {
				$index->interfaceExtends[$interfaceName][$info->nameLower] = $info;
			}

		} else {
			$index->interfaceExtends[''][$info->nameLower] = $info;
		}
	}


	private function indexTrait(TraitInfo $info, Index $index): void
	{
		$index->trait[$info->nameLower] = $info;
		$index->namespace[$info->namespaceLower]->trait[$info->nameShortLower] = $info;
	}


	private function indexInstanceOf(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->instanceOf[$info->nameLower])) {
			return; // already computed
		}

		$index->instanceOf[$info->nameLower] = [$info->nameLower => $info];
		foreach ([$index->classExtends, $index->classImplements, $index->interfaceExtends] as $edges) {
			foreach ($edges[$info->nameLower] ?? [] as $childInfo) {
				$this->indexInstanceOf($index, $childInfo);
				$index->instanceOf[$info->nameLower] += $index->instanceOf[$childInfo->nameLower];
			}
		}
	}


	private function indexClassMethodOverrides(Index $index, ClassInfo $info, array $normal, array $abstract): void
	{
		$queue = $info->implements;
		while (!empty($queue)) {
			$interface = $index->interface[array_shift($queue)] ?? null;

			if ($interface === null) {
				continue; // TODO: missing guard
			}

			foreach ($interface->methods as $method) {
				$abstract[$method->nameLower] = $interface;
			}

			foreach ($interface->extends as $extend) {
				$queue[] = $extend;
			}
		}

		foreach ($info->methods as $method) {
			if ($method->private) {
				continue;
			}

			if (isset($normal[$method->nameLower])) {
				$ancestor = $normal[$method->nameLower];
				$index->methodOverrides[$info->nameLower][$method->nameLower][] = $ancestor;
				$index->methodOverriddenBy[$ancestor->nameLower][$method->nameLower][] = $info;
			}

			if (isset($abstract[$method->nameLower])) {
				$ancestor = $abstract[$method->nameLower];
				$index->methodImplements[$info->nameLower][$method->nameLower][] = $ancestor;
				$index->methodImplementedBy[$ancestor->nameLower][$method->nameLower][] = $info;
			}

			if ($method->abstract) {
				$abstract[$method->nameLower] = $info;

			} else {
				unset($abstract[$method->nameLower]);
				$normal[$method->nameLower] = $info;
			}
		}

		foreach ($index->classExtends[$info->nameLower] ?? [] as $child) {
			$this->indexClassMethodOverrides($index, $child, $normal, $abstract);
		}
	}


	private function sort(Index $index): void
	{
		ksort($index->classLike);
		ksort($index->class);
		ksort($index->interface);
		ksort($index->trait);

		foreach ($index->classExtends as &$arr) {
			ksort($arr);
		}

		foreach ($index->classImplements as &$arr) {
			ksort($arr);
		}

		foreach ($index->interfaceExtends as &$arr) {
			ksort($arr);
		}

		foreach ($index->namespace as $namespaceIndex) {
			ksort($namespaceIndex->class);
			ksort($namespaceIndex->interface);
			ksort($namespaceIndex->trait);
			ksort($namespaceIndex->exception);
			ksort($namespaceIndex->children);
		}
	}
}
