<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use PhpParser\Node\Expr;


final class ConstantInfo extends MemberInfo
{
	/** @var Expr */
	public Expr $value;


	public function __construct(string $name, Expr $value)
	{
		parent::__construct($name);
		$this->value = $value;
	}
}
