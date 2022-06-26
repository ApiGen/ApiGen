<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use ApiGenX\Index\Index;
use ApiGenX\Info\Traits\HasGenericParameters;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;

use function strtolower;


class MethodInfo extends MemberInfo
{
	use HasGenericParameters;


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


	public function getEffectiveDescription(Index $index, ClassLikeInfo $classLike): string
	{
		$description = parent::getEffectiveDescription($index, $classLike);

		if ($description !== '') {
			return $description;
		}

		$ancestorLists = [
			$index->methodOverrides[$classLike->name->fullLower][$this->nameLower] ?? [],
			$index->methodImplements[$classLike->name->fullLower][$this->nameLower] ?? [],
		];

		foreach ($ancestorLists as $ancestorList) {
			foreach ($ancestorList as $ancestor) {
				$description = $ancestor->methods[$this->nameLower]->getEffectiveDescription($index, $ancestor);

				if ($description !== '') {
					return $description;
				}
			}
		}

		return '';
	}
}
