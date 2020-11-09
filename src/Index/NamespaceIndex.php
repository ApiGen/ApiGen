<?php declare(strict_types = 1);

namespace ApiGenX\Index;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\NameInfo;
use ApiGenX\Info\TraitInfo;


final class NamespaceIndex // TODO: split to NamespaceIndex + NamespaceInfo?
{
	/** @var NameInfo */
	public NameInfo $name;

	/** @var ClassInfo[] indexed by [className] */
	public array $class = [];

	/** @var InterfaceInfo[] indexed by [interfaceName] */
	public array $interface = [];

	/** @var TraitInfo[] indexed by [traitName] */
	public array $trait = [];

	/** @var ClassInfo[] indexed by [exceptionName] */
	public array $exception = [];

	/** @var NamespaceIndex[] indexed by [namespaceName] */
	public array $children = [];


	public function __construct(string $name)
	{
		$this->name = new NameInfo($name);
	}
}
