<?php declare(strict_types = 1);

namespace ApiGen\Info;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;


class ConstantInfo extends MemberInfo
{
	/** @var TypeNode|null */
	public ?TypeNode $type = null;

	/** @var ExprInfo */
	public ExprInfo $value;

	/** @var bool */
	public bool $final = false;


	public function __construct(string $name, ExprInfo $value)
	{
		parent::__construct($name);
		$this->value = $value;
	}
}
