<?php declare(strict_types = 1);

namespace ApiGenX\Info;


/**
 * @property-read bool $primary
 */
interface ElementInfo
{
	public function isDeprecated(): bool;
}
