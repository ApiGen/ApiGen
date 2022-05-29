<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;


final class ParameterInfo
{
	/** @var string */
	public string $name;

	/** @var string */
	public string $description = '';

	/** @var TypeNode|null */
	public ?TypeNode $type = null;

	/** @var bool */
	public bool $byRef = false;

	/** @var bool */
	public bool $variadic = false;

	/** @var ExprInfo|null */
	public ?ExprInfo $default = null;


	public function __construct(string $name)
	{
		$this->name = $name;
	}
}
