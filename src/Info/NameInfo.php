<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class NameInfo
{
	/** @var string e.g. 'ApiGenX\Info\Traits\HasName' */
	public string $full;

	/** @var string e.g. 'apigenx\info\traits\hasname' */
	public string $fullLower;

	/** @var string e.g. 'HasName' */
	public string $short;

	/** @var string e.g. 'hasname' */
	public string $shortLower;

	/** @var string e.g. 'ApiGenX\Info\Traits' */
	public string $namespace;

	/** @var string e.g. 'apigenx\info\traits' */
	public string $namespaceLower;


	public function __construct(string $name)
	{
		$pos = strrpos($name, '\\');

		$this->full = $name;
		$this->fullLower = strtolower($name);

		$this->short = $pos === false ? $name : substr($name, $pos + 1);
		$this->shortLower = strtolower($this->short);

		$this->namespace = $pos === false ? '' : substr($name, 0, $pos);
		$this->namespaceLower = strtolower($this->namespace);
	}
}
