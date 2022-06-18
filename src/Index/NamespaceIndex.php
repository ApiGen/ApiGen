<?php declare(strict_types = 1);

namespace ApiGenX\Index;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ElementInfo;
use ApiGenX\Info\EnumInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\ClassLikeNameInfo;
use ApiGenX\Info\TraitInfo;


final class NamespaceIndex implements ElementInfo
{
	/** @var ClassInfo[] indexed by [classShortName] (excludes exceptions) */
	public array $class = [];

	/** @var InterfaceInfo[] indexed by [interfaceShortName] */
	public array $interface = [];

	/** @var TraitInfo[] indexed by [traitShortName] */
	public array $trait = [];

	/** @var EnumInfo[] indexed by [enumShortName] */
	public array $enum = [];

	/** @var ClassInfo[] indexed by [exceptionShortName] */
	public array $exception = [];

	/** @var NamespaceIndex[] indexed by [namespaceShortName] */
	public array $children = [];


	public function __construct(
		public ClassLikeNameInfo $name,
		public bool $primary,
		public bool $deprecated,
	) {
	}


	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}
}
