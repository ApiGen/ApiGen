<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class EnumInfo extends ClassLikeInfo
{
	/** @var string|null */
	public ?string $scalarType;

	/** @var NameInfo[] indexed by [classLikeName] */
	public array $implements = [];

	/** @var EnumCaseInfo[] indexed by [caseName] */
	public array $cases = [];
}
