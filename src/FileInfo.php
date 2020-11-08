<?php declare(strict_types = 1);

namespace ApiGenX;


final class FileInfo
{
	public string $name;

	public bool $primary;


	public function __construct(string $name, bool $primary)
	{
		$this->name = $name;
		$this->primary = $primary;
	}
}
