<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;


trait HasName
{
	/** @var string e.g. 'ApiGenX\Info\Traits\HasName' */
	public string $name;

	/** @var string e.g. 'apigenx\info\traits\hasname' */
	public string $nameLower;

	/** @var string e.g. 'HasName' */
	public string $nameShort;

	/** @var string e.g. 'hasname' */
	public string $nameShortLower;

	/** @var string e.g. 'ApiGenX\Info\Traits' */
	public string $namespace;

	/** @var string e.g. 'apigenx\info\traits' */
	public string $namespaceLower;


	private function initName(string $name): void
	{
		$pos = strrpos($name, '\\');

		$this->name = $name;
		$this->nameLower = strtolower($name);

		$this->nameShort = $pos === false ? $name : substr($name, $pos + 1);
		$this->nameShortLower = strtolower($this->nameShort);

		$this->namespace = $pos === false ? '' : substr($name, 0, $pos);
		$this->namespaceLower = strtolower($this->namespace);
	}
}
