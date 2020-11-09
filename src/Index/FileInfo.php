<?php declare(strict_types = 1);

namespace ApiGenX\Index;


final class FileInfo // TODO: FileIndex? move to Info namespace?
{
	public string $name;

	public bool $primary; // TODO: one file MAY contain both primary and non-primary class-likes?


	public function __construct(string $name, bool $primary)
	{
		$this->name = $name;
		$this->primary = $primary;
	}
}
