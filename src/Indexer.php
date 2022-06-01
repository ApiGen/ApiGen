<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Index\FileIndex;
use ApiGenX\Index\Index;
use ApiGenX\Index\NamespaceIndex;
use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ClassLikeInfo;
use ApiGenX\Info\EnumInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\MissingInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\TraitInfo;


final class Indexer
{
	public function indexFile(Index $index, ?string $file, bool $primary): void
	{
		if ($file === null) {
			$file = '';
		}

		if (isset($index->files[$file])) {
			$index->files[$file]->primary = $index->files[$file]->primary || $primary;
			return;
		}

		$index->files[$file] = new FileIndex($file, $primary);
	}


	public function indexNamespace(Index $index, string $namespace, string $namespaceLower, bool $primary): void
	{
		if (isset($index->namespace[$namespaceLower])) {
			if ($primary && !$index->namespace[$namespaceLower]->primary) {
				for ($info = $index->namespace[$namespaceLower]; $info->name->full !== ''; $info = $index->namespace[$info->name->namespaceLower]) {
					$info->primary = true;
				}
			}

			return;
		}

		$info = new NamespaceIndex(new NameInfo($namespace, $namespaceLower), $primary);

		if ($namespaceLower !== '') {
			$primary = $primary && $info->name->namespaceLower !== '';
			$this->indexNamespace($index, $info->name->namespace, $info->name->namespaceLower, $primary);
		}

		$index->namespace[$namespaceLower] = $info;
		$index->namespace[$info->name->namespaceLower]->children[$info->name->shortLower] = $info;
	}


	public function indexClassLike(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->classLike[$info->name->fullLower])) {
			return; // ignore duplicates (TODO: emit warning?)
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

		} elseif ($info instanceof EnumInfo) {
			$this->indexEnum($info, $index);

		} elseif ($info instanceof MissingInfo) {
			$this->indexMissing($info, $index);

		} else {
			throw new \LogicException();
		}
	}


	public function postProcess(Index $index): void
	{
		// DAG
		$this->indexDirectedAcyclicGraph($index);

		// instance of
		foreach ([$index->class, $index->interface, $index->enum] as $infos) {
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


	private function indexEnum(EnumInfo $info, Index $index): void
	{
		$index->enum[$info->name->fullLower] = $info;
		$index->files[$info->file ?? '']->classLike[$info->name->fullLower] = $info;
		$index->namespace[$info->name->namespaceLower]->enum[$info->name->shortLower] = $info;

		foreach ($info->implements as $interfaceNameLower => $interfaceName) {
			$index->enumImplements[$interfaceNameLower][$info->name->fullLower] = $info;
		}
	}


	private function indexMissing(MissingInfo $info, Index $index): void
	{
		// nothing to index
	}


	private function indexDirectedAcyclicGraph(Index $index): void
	{
		$dag = array_merge_recursive($index->classExtends, $index->classImplements, $index->classUses, $index->interfaceExtends, $index->enumImplements);

		$findCycle = static function (array $node, array $visited) use ($index, $dag, &$findCycle): void {
			foreach ($node as $childKey => $_) {
				if (isset($visited[$childKey])) {
					$path = [...array_keys($visited), $childKey];
					$path = array_map(fn (string $item) => $index->classLike[$item]->name->full, $path);
					throw new \RuntimeException("Invalid directed acyclic graph because it contains cycle:\n" . implode(' -> ', $path));

				} else {
					$findCycle($dag[$childKey] ?? [], $visited + [$childKey => true]);
				}
			}
		};

		foreach ($dag as $nodeKey => $node) {
			$findCycle($node, [$nodeKey => true]);
		}

		$index->dag = $dag;
	}


	private function indexInstanceOf(Index $index, ClassLikeInfo $info): void
	{
		if (isset($index->instanceOf[$info->name->fullLower])) {
			return; // already computed
		}

		$index->instanceOf[$info->name->fullLower] = [$info->name->fullLower => $info];
		foreach ([$index->classExtends, $index->classImplements, $index->interfaceExtends, $index->enumImplements] as $edges) {
			foreach ($edges[$info->name->fullLower] ?? [] as $childInfo) {
				$this->indexInstanceOf($index, $childInfo);
				$index->instanceOf[$info->name->fullLower] += $index->instanceOf[$childInfo->name->fullLower];
			}
		}
	}


	/**
	 * @param ClassInfo[]     $normal   indexed by [methodName]
	 * @param ClassLikeInfo[] $abstract indexed by [methodName]
	 */
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
		ksort($index->files);
		ksort($index->namespace);
		ksort($index->classLike);
		ksort($index->class);
		ksort($index->interface);
		ksort($index->trait);
		ksort($index->enum);

		foreach ($index->classExtends as &$arr) {
			ksort($arr);
		}

		foreach ($index->classImplements as &$arr) {
			ksort($arr);
		}

		foreach ($index->classUses as &$arr) {
			ksort($arr);
		}

		foreach ($index->interfaceExtends as &$arr) {
			ksort($arr);
		}

		foreach ($index->enumImplements as &$arr) {
			ksort($arr);
		}

		foreach ($index->namespace as $namespaceIndex) {
			ksort($namespaceIndex->class);
			ksort($namespaceIndex->interface);
			ksort($namespaceIndex->trait);
			ksort($namespaceIndex->enum);
			ksort($namespaceIndex->exception);
			ksort($namespaceIndex->children);
		}

		// move root namespace to end
		$rootNamespace = $index->namespace[''];
		unset($index->namespace[''], $rootNamespace->children['']);
		$index->namespace[''] = $rootNamespace;
		$rootNamespace->children[''] = $rootNamespace;
	}
}
