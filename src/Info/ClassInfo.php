<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;


final class ClassInfo extends ClassLikeInfo
{
	/** @var bool */
	public bool $abstract = false;

	/** @var bool */
	public bool $final = false;

	/** @var NameInfo|null */
	public ?NameInfo $extends = null;

	/** @var NameInfo[] indexed by [classLikeName] */
	public array $implements = [];

	/** @var NameInfo[] indexed by [classLikeName] */
	public array $uses = [];


	public function __construct(NameInfo $name)
	{
		parent::__construct(
			$name,
			class: true,
			interface: false,
			trait: false,
		);
	}


	/**
	 * @return iterable<ClassInfo>
	 */
	public function ancestors(Index $index): iterable
	{
		if ($this->extends) {
			$parent = $index->class[$this->extends->fullLower];

			yield $parent;
			yield from $parent->ancestors($index);
		}
	}


	/**
	 * @return iterable<ClassInfo>
	 */
	public function indirectDescendants(Index $index): iterable
	{
		foreach ($index->classExtends[$this->name->fullLower] ?? [] as $descendant) {
			yield from $index->classExtends[$descendant->name->fullLower] ?? [];
			yield from $descendant->indirectDescendants($index);
		}
	}
}
