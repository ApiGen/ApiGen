<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;
use ApiGenX\Info\Traits\HasLineLocation;
use ApiGenX\Info\Traits\HasTags;
use ApiGenX\Info\Traits\HasVisibility;


abstract class MemberInfo
{
	use HasTags;
	use HasLineLocation;
	use HasVisibility;


	/** @var string */
	public string $name;

	/** @var bool */
	public bool $magic = false;


	public function __construct(string $name)
	{
		$this->name = $name;
	}


	public function getEffectiveDescription(Index $index, ClassLikeInfo $classLike): string
	{
		return $this->description;
	}
}
