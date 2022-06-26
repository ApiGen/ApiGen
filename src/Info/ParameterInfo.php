<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;


class ParameterInfo
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


	public function getEffectiveDescription(Index $index, ClassLikeInfo $classLike, MethodInfo $method): string
	{
		$description = $this->description;

		if ($description !== '') {
			return $description;
		}

		$ancestorLists = [
			$index->methodOverrides[$classLike->name->fullLower][$method->nameLower] ?? [],
			$index->methodImplements[$classLike->name->fullLower][$method->nameLower] ?? [],
		];

		foreach ($ancestorLists as $ancestorList) {
			foreach ($ancestorList as $ancestor) {
				$ancestorMethod = $ancestor->methods[$method->nameLower];
				$ancestorParameter = $ancestorMethod->parameters[$this->name] ?? null;
				$description = $ancestorParameter?->getEffectiveDescription($index, $ancestor, $ancestorMethod) ?? '';

				if ($description !== '') {
					return $description;
				}
			}
		}

		return '';
	}
}
