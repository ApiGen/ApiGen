<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class TraitInfo extends ClassLikeInfo
{
	/** @var ClassLikeReferenceInfo[] indexed by [classLikeName] */
	public array $uses = [];
}
