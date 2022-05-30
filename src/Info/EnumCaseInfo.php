<?php declare(strict_types = 1);

namespace ApiGenX\Info;


/**
 * TODO: enum case cannot have visibility
 */
final class EnumCaseInfo extends MemberInfo
{
	public function __construct(
		string $name,
		public ?ExprInfo $value,
	) {
		parent::__construct($name);
	}
}
