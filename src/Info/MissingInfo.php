<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class MissingInfo extends ClassLikeInfo
{
	public function __construct(
		ClassLikeNameInfo $name,
		public ClassLikeNameInfo $referencedBy,
	) {
		parent::__construct(
			$name,
			primary: false,
		);
	}
}
