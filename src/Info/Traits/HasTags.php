<?php declare(strict_types = 1);

namespace ApiGenX\Info\Traits;


trait HasTags
{
	/** @var string */
	public string $description = '';

	/** @var string[][] indexed by [tagName][]  */
	public array $tags = [];


	public function isDeprecated(): bool
	{
		return isset($this->tags['deprecated']);
	}
}
