<?php declare(strict_types = 1);

namespace ApiGenX\Info;

use function strtolower;


final class ClassLikeReferenceInfo
{
	/** @var string e.g. 'ApiGenX\Info\Traits\HasName' */
	public string $full;

	/** @var string e.g. 'apigenx\info\traits\hasname' */
	public string $fullLower;


	public function __construct(string $full, ?string $fullLower = null)
	{
		$this->full = $full;
		$this->fullLower = $fullLower ?? strtolower($full);
	}
}
