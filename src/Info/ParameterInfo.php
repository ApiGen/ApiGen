<?php declare(strict_types = 1);

namespace ApiGen\Info;

use ApiGen\Index\Index;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

use function array_values;


class ParameterInfo
{
	/** @var string */
	public string $name;

	/** @var int */
	public int $position;

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


	public function __construct(string $name, int $position)
	{
		$this->name = $name;
		$this->position = $position;
	}


	public function getEffectiveDescription(Index $index, ClassLikeInfo $classLike, MethodInfo $method): string
	{
		if ($this->description !== '') {
			return $this->description;
		}

		foreach ($method->ancestors($index, $classLike) as $ancestor) {
			$ancestorMethod = $ancestor->methods[$method->nameLower];
			$ancestorParameter = array_values($ancestorMethod->parameters)[$this->position] ?? null;
			$description = $ancestorParameter?->getEffectiveDescription($index, $ancestor, $ancestorMethod) ?? '';

			if ($description !== '') {
				return $description;
			}
		}

		return '';
	}
}
