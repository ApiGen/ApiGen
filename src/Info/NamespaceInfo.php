<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class NamespaceInfo
{
	/** @var string */
	public string $name;

	/** @var string */
	public string $nameLower;

	/** @var string */
	public string $nameShort;

	/** @var string */
	public string $nameShortLower;

	/** @var string */
	public string $parent;

	/** @var string */
	public string $parentLower;
}
