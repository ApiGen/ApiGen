<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;


final class PropertyInfo extends MemberInfo
{
	/** @var ExprInfo|null */
	public ?ExprInfo $default = null;

	/** @var TypeNode|null */
	public ?TypeNode $type = null;

	/** @var bool */
	public bool $static = false;

	/** @var bool */
	public bool $readOnly = false;

	/** @var bool */
	public bool $writeOnly = false;
}
