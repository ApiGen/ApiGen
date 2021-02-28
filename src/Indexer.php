<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\TraitInfo;


final class Indexer
{
	public function indexFile(Index $index, ?string $file, bool $primary): void
	{
		$file = $file === null ? '' : realpath($file);
		$index->files[$file] ??= new FileIndex($file, $primary);
	}


	public function indexNamespace(Index $index, string $namespace, string $namespaceLower): void
	{
		if (isset($index->namespace[$namespaceLower])) {
			return;
		}

		$info = new NamespaceIndex($namespace);

		if ($namespaceLower !== '') {
			$this->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower);
		}

		$index->namespace[$namespaceLower] = $info;
		$index->namespace[$info->name->namespaceLower]->children[$namespaceLower] = $info;
	}


	public function indexClassLike(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->classLike[$info->name->fullLower])) {
			return; // ignore duplicates
		}

		$index->classLike[$info->name->fullLower] = $info;

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
					unset($namespaceIndex->class[$info->name->shortLower]);
					$namespaceIndex->exception[$info->name->shortLower] = $info;
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
		$index->class[$info->name->fullLower] = $info;
		$index->files[$info->file ?? '']->classLike[$info->name->fullLower] = $info;
		$index->namespace[$info->name->namespaceLower]->class[$info->name->shortLower] = $info;
		$index->classExtends[$info->extends ? $info->extends->fullLower : ''][$info->name->fullLower] = $info;

		foreach ($info->implements as $interfaceNameLower => $interfaceName) {
			$index->classImplements[$interfaceNameLower][$info->name->fullLower] = $info;
		}

		foreach ($info->uses as $traitNameLower => $traitName) {
			$index->classUses[$traitNameLower][$info->name->fullLower] = $info;
		}
	}


	private function indexInterface(InterfaceInfo $info, Index $index): void
	{
		$index->interface[$info->name->fullLower] = $info;
		$index->files[$info->file ?? '']->classLike[$info->name->fullLower] = $info;
		$index->namespace[$info->name->namespaceLower]->interface[$info->name->shortLower] = $info;

		if ($info->extends) {
			foreach ($info->extends as $interfaceNameLower => $interfaceName) {
				$index->interfaceExtends[$interfaceNameLower][$info->name->fullLower] = $info;
			}

		} else {
			$index->interfaceExtends[''][$info->name->fullLower] = $info;
		}
	}


	private function indexTrait(TraitInfo $info, Index $index): void
	{
		$index->trait[$info->name->fullLower] = $info;
		$index->files[$info->file ?? '']->classLike[$info->name->fullLower] = $info;
		$index->namespace[$info->name->namespaceLower]->trait[$info->name->shortLower] = $info;
	}


	private function indexInstanceOf(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->instanceOf[$info->name->fullLower])) {
			return; // already computed
		}

		$index->instanceOf[$info->name->fullLower] = [$info->name->fullLower => $info];
		foreach ([$index->classExtends, $index->classImplements, $index->interfaceExtends] as $edges) {
			foreach ($edges[$info->name->fullLower] ?? [] as $childInfo) {
				$this->indexInstanceOf($index, $childInfo);
				$index->instanceOf[$info->name->fullLower] += $index->instanceOf[$childInfo->name->fullLower];
			}
		}
	}


	private function indexClassMethodOverrides(Index $index, ClassInfo $info, array $normal, array $abstract): void
	{
		$queue = array_keys($info->implements);
		while (!empty($queue)) {
			$interface = $index->interface[array_shift($queue)] ?? null;

			if ($interface === null) {
				continue; // TODO: missing guard
			}

			foreach ($interface->methods as $method) {
				$abstract[$method->nameLower] = $interface;
			}

			foreach ($interface->extends as $extendLower => $extend) {
				$queue[] = $extendLower;
			}
		}

		foreach ($info->methods as $method) {
			if ($method->private) {
				continue;
			}

			if (isset($normal[$method->nameLower])) {
				$ancestor = $normal[$method->nameLower];
				$index->methodOverrides[$info->name->fullLower][$method->nameLower][] = $ancestor;
				$index->methodOverriddenBy[$ancestor->name->fullLower][$method->nameLower][] = $info;
			}

			if (isset($abstract[$method->nameLower])) {
				$ancestor = $abstract[$method->nameLower];
				$index->methodImplements[$info->name->fullLower][$method->nameLower][] = $ancestor;
				$index->methodImplementedBy[$ancestor->name->fullLower][$method->nameLower][] = $info;
			}

			if ($method->abstract) {
				$abstract[$method->nameLower] = $info;

			} else {
				unset($abstract[$method->nameLower]);
				$normal[$method->nameLower] = $info;
			}
		}

		foreach ($index->classExtends[$info->name->fullLower] ?? [] as $child) {
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
