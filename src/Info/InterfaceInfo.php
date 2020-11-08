<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index;


final class InterfaceInfo extends ClassLikeInfo
{
	/** @var string[] */
	public array $extends;


	public function __construct(string $name)
	{
		parent::__construct($name);
		$this->class = false;
		$this->interface = true;
		$this->trait = false;
	}


	/**
	 * @return ClassInfo[]
	 */
	public function ancestors(Index $index): iterable
	{
		foreach ($this->extends as $extend) {
			if (isset($index->interface[$extend])) { // TODO: missing guard
				$parent = $index->interface[$extend];
				yield $parent;
				yield from $parent->ancestors($index);
			}
		}
	}
}
