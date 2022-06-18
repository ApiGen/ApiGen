<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;


final class TraitInfo extends ClassLikeInfo
{
	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $uses = [];


	/**
	 * @return iterable<ClassInfo>
	 */
	public function indirectUses(Index $index): iterable
	{
		foreach ($index->classUses[$this->name->fullLower] ?? [] as $directUser) {
			yield from $index->classExtends[$directUser->name->fullLower] ?? [];
			yield from $directUser->indirectDescendants($index);
		}
	}
}
