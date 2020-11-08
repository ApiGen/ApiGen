<?php declare(strict_types = 1);

namespace ApiGenX;

use ApiGenX\Info\ClassInfo;
use ApiGenX\Info\InterfaceInfo;
use ApiGenX\Info\TraitInfo;
use ApiGenX\Info\Traits\HasName;


final class NamespaceInfo
{
	use HasName;

	/** @var ClassInfo[] indexed by [className] */
	public array $class = [];

	/** @var InterfaceInfo[] indexed by [interfaceName] */
	public array $interface = [];

	/** @var TraitInfo[] indexed by [traitName] */
	public array $trait = [];

	/** @var ClassInfo[] indexed by [exceptionName] */
	public array $exception = [];

	/** @var NamespaceInfo[] indexed by [namespaceName] */
	public array $children = [];


	public function __construct(string $name)
	{
		$this->initName($name);
	}
}
