<?php declare(strict_types = 1);

namespace ApiGenTests\Data\Features\Php71\ClassConstantVisibility;


class ClassConstantVisibility
{
	const DEFAULT_CONSTANT = 0;

	public const PUBLIC_CONSTANT = 'public';
	protected const PROTECTED_CONSTANT = 'protected';
	private const PRIVATE_CONSTANT = 'private';
}
