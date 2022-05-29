<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class ConstantInfo extends MemberInfo
{
	/** @var ExprInfo */
	public ExprInfo $value;


	public function __construct(string $name, ExprInfo $value)
	{
		parent::__construct($name);
		$this->value = $value;
	}
}
