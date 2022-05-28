<?php declare(strict_types = 1);

namespace ApiGenX\Index;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\ElementInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\MissingInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\TraitInfo;


final class NamespaceIndex implements ElementInfo
{
	/** @var ClassInfo[] indexed by [className] */
	public array $class = [];

	/** @var InterfaceInfo[] indexed by [interfaceName] */
	public array $interface = [];

	/** @var TraitInfo[] indexed by [traitName] */
	public array $trait = [];

	/** @var ClassInfo[] indexed by [exceptionName] */
	public array $exception = [];

	/** @var NamespaceIndex[] indexed by [namespaceShortName] */
	public array $children = [];


	public function __construct(
		public NameInfo $name,
		public bool $primary,
	) {
	}


	public function isDeprecated(): bool
	{
		return false; // TODO?
	}
}
