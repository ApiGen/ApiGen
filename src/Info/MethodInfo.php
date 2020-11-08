<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use PHPStan\PhpDocParser\Ast\Type\TypeNode;


final class MethodInfo extends MemberInfo
{
	/** @var string */
	public string $nameLower;

	/** @var ParameterInfo[] indexed by [parameterName] */
	public array $parameters = [];

	/** @var TypeNode|null */
	public ?TypeNode $returnType = null;

	/** @var bool */
	public bool $byRef = false;

	/** @var bool */
	public bool $static = false;

	/** @var bool */
	public bool $abstract = false;

	/** @var bool */
	public bool $final = false;


	public function __construct(string $name)
	{
		parent::__construct($name);
		$this->nameLower = strtolower($name);
	}
}
