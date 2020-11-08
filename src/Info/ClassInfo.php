<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index;


final class ClassInfo extends ClassLikeInfo
{
	/** @var bool */
	public bool $abstract = false;

	/** @var bool */
	public bool $final = false;

	/** @var string|null */
	public ?string $extends = null;

	/** @var string[] */
	public array $implements = [];

	/** @var string[] */
	public array $uses = [];


	public function __construct(string $name)
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
			$parent = $index->class[$this->extends];

			yield $parent;
			yield from $parent->ancestors($index);
		}
	}
}
