<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;
use ApiGenX\Info\Traits\HasDependencies;
use ApiGenX\Info\Traits\HasLineLocation;
use ApiGenX\Info\Traits\HasTags;


abstract class ClassLikeInfo
{
	use HasTags;
	use HasLineLocation;
	use HasDependencies;

	/** @var NameInfo */
	public NameInfo $name;

	/** @var bool */
	public bool $class;

	/** @var bool */
	public bool $interface;

	/** @var bool */
	public bool $trait;

	/** @var bool */
	public bool $primary = true;

	/** @var string|null */
	public ?string $file = null;

	/** @var ConstantInfo[] indexed by [constantName] */
	public array $constants = [];

	/** @var PropertyInfo[] indexed by [propertyName] */
	public array $properties = [];

	/** @var MethodInfo[] indexed by [methodName] */
	public array $methods = [];


	public function __construct(NameInfo $name)
	{
		$this->name = $name;
	}


	public function isInstanceOf(Index $index, string $type): bool
	{
		return isset($index->instanceOf[$type][$this->name->fullLower]);
	}


	public function isThrowable(Index $index): bool
	{
		return $this->isInstanceOf($index, 'throwable');
	}
}
