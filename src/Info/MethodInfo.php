<?php declare(strict_types = 1);

namespace ApiGen\Info;

use ApiGen\Index\Index;
use ApiGen\Info\Traits\HasGenericParameters;
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

	/** @var string */
	public string $returnDescription = '';

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
		if ($this->description !== '') {
			return $this->description;
		}

		foreach ($this->ancestors($index, $classLike) as $ancestor) {
			$description = $ancestor->methods[$this->nameLower]->getEffectiveDescription($index, $ancestor);

			if ($description !== '') {
				return $description;
			}
		}

		return '';
	}


	public function getEffectiveReturnDescription(Index $index, ClassLikeInfo $classLike): string
	{
		if ($this->returnDescription !== '') {
			return $this->returnDescription;
		}

		foreach ($this->ancestors($index, $classLike) as $ancestor) {
			$description = $ancestor->methods[$this->nameLower]->getEffectiveReturnDescription($index, $ancestor);

			if ($description !== '') {
				return $description;
			}
		}

		return '';
	}


	/**
	 * @return iterable<ClassLikeInfo>
	 */
	public function ancestors(Index $index, ClassLikeInfo $classLike): iterable
	{
		yield from $index->methodOverrides[$classLike->name->fullLower][$this->nameLower] ?? [];
		yield from $index->methodImplements[$classLike->name->fullLower][$this->nameLower] ?? [];
	}
}
