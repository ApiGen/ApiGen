<?php declare(strict_types = 1);

namespace ApiGenX\Analyzer;


class AnalyzeTask
{
	public function __construct(
		public string $sourceFile,
		public bool $primary,
	) {
	}
}
