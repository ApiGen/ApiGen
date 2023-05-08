<?php declare(strict_types = 1);

namespace ApiGenTests\Analyzer\Data\BodySkippingLexer;

use function foo\math\{cos, cosh, sin};
use foo\math\{ const PI, function sinX, function cosX, function coshX };
use function strlen;


abstract class BasicClass
{
	public function a(int $a = 123): void
	{
		echo strlen('hello');
	}


	abstract function b();


	public static function c(): void
	{
		echo 'hello';
	}
}
