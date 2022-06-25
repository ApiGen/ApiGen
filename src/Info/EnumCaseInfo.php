<?php declare(strict_types = 1);

namespace ApiGenX\Info;


class EnumCaseInfo extends MemberInfo
{
	public function __construct(
		string $name,
		public ?ExprInfo $value,
	) {
		parent::__construct($name);
	}
}
