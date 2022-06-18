<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;
use ApiGenX\Info\Traits\HasDependencies;
use ApiGenX\Info\Traits\HasLineLocation;
use ApiGenX\Info\Traits\HasTags;


abstract class ClassLikeInfo implements ElementInfo
{
	use HasTags;
	use HasLineLocation;
	use HasDependencies;


	/** @var string|null */
	public ?string $file = null;

	/** @var ConstantInfo[] indexed by [constantName] */
	public array $constants = [];

	/** @var PropertyInfo[] indexed by [propertyName] */
	public array $properties = [];

	/** @var MethodInfo[] indexed by [methodName] */
	public array $methods = [];


	public function __construct(
		public NameInfo $name,
		public bool $primary,
	) {
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
