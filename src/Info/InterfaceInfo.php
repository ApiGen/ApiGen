<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;


final class InterfaceInfo extends ClassLikeInfo
{
	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $extends = [];


	/**
	 * @return iterable<InterfaceInfo>
	 */
	public function ancestors(Index $index): iterable
	{
		foreach ($this->extends as $extend) {
			if (isset($index->interface[$extend->fullLower])) { // TODO: missing guard
				$parent = $index->interface[$extend->fullLower];
				yield $parent;
				yield from $parent->ancestors($index);
			}
		}
	}
}
