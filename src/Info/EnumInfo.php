<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class EnumInfo extends ClassLikeInfo
{
	/** @var string|null */
	public ?string $scalarType;

	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $implements = [];

	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $uses = [];

	/** @var EnumCaseInfo[] indexed by [caseName] */
	public array $cases = [];
}
