<?php declare(strict_types = 1);

namespace ApiGen\Info;

use ApiGen\Index\Index;
use ApiGen\Info\Traits\HasLineLocation;
use ApiGen\Info\Traits\HasTags;
use ApiGen\Info\Traits\HasVisibility;


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
