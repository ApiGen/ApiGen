<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;


final class ClassInfo extends ClassLikeInfo
{
	/** @var bool */
	public bool $abstract = false;

	/** @var bool */
	public bool $final = false;

	/** @var ClassLikeReferenceInfo|null */
	public ?ClassLikeReferenceInfo $extends = null;

	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $implements = [];

	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $uses = [];


	/**
	 * @return iterable<ClassInfo>
	 */
	public function ancestors(Index $index): iterable // TODO: remove?
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
