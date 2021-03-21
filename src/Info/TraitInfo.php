<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;


final class TraitInfo extends ClassLikeInfo
{
	/** @var NameInfo[] indexed by [classLikeName] */
	public array $uses = [];


	public function __construct(NameInfo $name, bool $primary)
	{
		parent::__construct(
			$name,
			class: false,
			interface: false,
			trait: true,
			primary: $primary,
		);
	}


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
