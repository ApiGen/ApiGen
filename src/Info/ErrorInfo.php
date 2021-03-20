<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class ErrorInfo
{
	public const KIND_SYNTAX_ERROR = 'Syntax error';
	public const KIND_MISSING_SYMBOL = 'Missing class / trait / interface';

	public function __construct(
		public string $kind,
		public string $message,
	) {
	}
}
