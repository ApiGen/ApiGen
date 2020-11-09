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
		parent::__construct($name);
		$this->class = true;
		$this->interface = false;
		$this->trait = false;
	}


	/**
	 * @return ClassInfo[]
	 */
	public function ancestors(Index $index): iterable
	{
		if ($this->extends) {
			$parent = $index->class[$this->extends->fullLower];

			yield $parent;
			yield from $parent->ancestors($index);
		}
	}
}
