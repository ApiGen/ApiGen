<?php declare(strict_types = 1);

namespace ApiGen\Analyzer;

use ApiGen\Info\AliasInfo;
use ApiGen\Info\AliasReferenceInfo;
use ApiGen\Info\ClassLikeReferenceInfo;
use ApiGen\Info\GenericParameterInfo;


class NameContextFrame
{
	/** @var null|NameContextFrame */
	public ?NameContextFrame $parent = null;

	/** @var null|ClassLikeReferenceInfo */
	public ?ClassLikeReferenceInfo $scope = null;

	/** @var (AliasInfo|AliasReferenceInfo|GenericParameterInfo)[] indexed by [name] */
	public array $names = [];


	public function __construct(?NameContextFrame $parent)
	{
		$this->parent = $parent;
		$this->scope = $parent?->scope;
		$this->names = $parent?->names ?? [];
	}
}
