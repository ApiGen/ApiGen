<?php declare(strict_types = 1);

namespace ApiGenX\Info;


final class ErrorInfo
{
	public const KIND_SYNTAX_ERROR = 'syntax_error';
	public const KIND_MISSING_SYMBOL = 'missing_symbol';


	public function __construct(
		public string $kind,
		public string $message,
	) {
	}
}
