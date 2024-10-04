<?php declare(strict_types = 1);

namespace ApiGenTests;

use ApiGen\Info\Expr\StringExprInfo;
use Tester\Assert;
use Tester\Environment;
use Tester\TestCase;

require __DIR__ . '/../vendor/autoload.php';


/**
 * @testCase
 */
class StringExprInfoTest extends TestCase
{
	/**
	 * @dataProvider provideToStringData
	 */
	public function testToString(string $value, string $expected): void
	{
		$actual = (new StringExprInfo($value))->toString();
		Assert::same($expected, $actual);
	}


	/**
	 * @return iterable<array{string, string}>
	 */
	public function provideToStringData(): iterable
	{
		yield ['foo', "'foo'"];
		yield ['foo\\bar', '"foo\\\\bar"'];
		yield ['foo\'bar', '"foo\'bar"'];
		yield ['foo"bar', '"foo\\"bar"'];
		yield ["foo\nbar", '"foo\\nbar"'];
		yield ["foo\r\n\tbar", '"foo\\r\\n\\tbar"'];
		yield ["foo\x00bar", '"foo\\x00bar"'];
		yield ["foo\u{0200B}bar", '"foo\\u{200B}bar"'];
		yield ["foo\x00\u{0200B}bar", '"foo\\x00\\u{200B}bar"'];
	}
}


Environment::setup();
(new StringExprInfoTest)->run();
