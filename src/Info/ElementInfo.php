<?php declare(strict_types = 1);

namespace ApiGenX\Info;


interface ElementInfo
{
	public function isDeprecated(): bool;
}
