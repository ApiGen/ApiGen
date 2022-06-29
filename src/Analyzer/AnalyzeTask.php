<?php declare(strict_types = 1);

namespace ApiGen\Analyzer;


class AnalyzeTask
{
	public function __construct(
		public string $sourceFile,
		public bool $primary,
	) {
	}
}
