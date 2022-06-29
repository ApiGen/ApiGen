<?php declare(strict_types = 1);

namespace ApiGen\Info;


class ErrorInfo
{
	public const KIND_SYNTAX_ERROR = 'syntax_error';
	public const KIND_MISSING_SYMBOL = 'missing_symbol';
	public const KIND_DUPLICATE_SYMBOL = 'duplicate_symbol';


	public function __construct(
		public string $kind,
		public string $message,
	) {
	}
}
